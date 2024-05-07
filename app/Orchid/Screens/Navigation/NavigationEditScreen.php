<?php

namespace App\Orchid\Screens\Navigation;


use App\Models\Menu;
use Orchid\Screen\TD;
use App\Models\MenuItem;
use Orchid\Screen\Screen;
use Illuminate\Http\Request;
use App\Models\NavigationItem;
use App\Models\Navigation;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Alert;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\TextArea;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\ModalToggle;
use App\Models\NavigationItemTranslation;


class NavigationEditScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Edit Navigation';

    /**
     * @var Navigation
     */
    public $Navigation;


    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'Edit navigation items';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(NavigationItem $NavigationItems): array
    {
        $id = request()->route('id');
        $NavigationItems = NavigationItem::where('navigation_id', $id)
            ->with(['translations' => function ($query) {
                $query->where('locale', 'cs');
            }])->get();

        // dd($NavigationItems);
        return [
            'NavigationItems' => $NavigationItems
        ];
    }


    public function mount($id)
    {
        // dd('mount method called');
        $id = request()->route('id');
        $this->Navigation = Navigation::find($id);
    }
    /**
     * Button commands.
     *
     * @return Action[]
     */
    public function commandBar(): array
    {
        $request = app('request');
        $id = $request->route('id');
        // dd($id);
        return [
            //  dd($this),
            ModalToggle::make('Vytvořit položku Menu')
                ->modal('CreateMenuItem')
                ->method('saveMenuItem')
                ->icon('plus')
                ->parameters([
                    'NavigationItem_id' => $id,
                ]),
        ];
    }

    /**
     * Views.
     *
     * @return Layout[]
     */
    public function layout(): array
    {


        return [
            Layout::table('NavigationItems', [
                TD::make()
                    ->width('40px')
                    ->class('page-move')
                    ->render(function ($NavigationItem) {
                        //return   '<div class="page-move w-6"></div>';
                    }),
                TD::make('title', __('Title'))
                    ->sort()
                    ->filter(TD::FILTER_TEXT)

                    ->render(function ($NavigationItem) {

                        $text = ($NavigationItem->translations->first()->title);

                        return   "<div > $text</div>";
                    }),
                TD::make(__('Actions'))
                    ->align(TD::ALIGN_CENTER)
                    ->width('200px')
                    ->render(function ($NavigationItems) {
                        return Dropdown::make(__('Actions'))
                            ->icon('options-vertical')
                            ->list([
                                ModalToggle::make(__('Edit'))
                                    ->modal('EditMenuItem')
                                    ->method('updateMenuItem')
                                    ->icon('pencil')
                                    ->parameters(['id' => $NavigationItems->id]),
                                Button::make(__('Delete'))
                                    ->method('remove')
                                    ->icon('trash')
                                    ->parameters(['id' => $NavigationItems->id]),
                            ]);
                    }),

            ]),

            Layout::modal('CreateMenuItem', [
                Layout::rows([
                    Input::make('menuCategoryItem.name')
                        ->title('Název')
                        ->placeholder('')
                        ->required(),
                    Input::make('menuCategoryItem.url')
                        ->title('URL')
                        ->placeholder('')
                        ->required(),
                ])
            ])->title('Vytvorit Položku')
                ->method('saveMenuItem')
                ->applyButton('Přidat'),

            layout::modal('EditMenuItem', [
                Layout::rows([
                    Input::make('menuCategoryItem.name')
                        ->title('Název')
                        ->placeholder('')
                        ->required(),

                    Input::make('menuCategoryItem.url')
                        ->title('URL')
                        ->placeholder('')
                        ->required(),

                ])
            ])->title('Upravit Položku')
                ->method('updateMenuItem')
                ->applyButton('Upravit')
                ->async('asyncloadMenuItemData'),

        ];
    }

    public function saveMenuItem(Request $request, $NavigationItem_id)
    {
        $request->validate([
            'menuCategoryItem.name' => 'required|string',
            'menuCategoryItem.url' => 'required|string',
        ]);

        $category = new NavigationItem();

        $category->url = $request['menuCategoryItem']['url'];
        $category->navigation_id =  $NavigationItem_id;
        //dd($category);
        $category->save();
        $translation = new NavigationItemTranslation();
        $translation->title = $request['menuCategoryItem']['name'];
        $translation->locale = 'cs';
        $translation->navigation_item_id = $category->id;
        // dd($category, $translation);

        $translation->save();
        Alert::success('Položka byla úspěšně uložena.');
        return back();
    }




    public function remove(Request $request)
    {
        //   dd($request->input('id'));
        $NavigationItem = NavigationItem::findOrFail($request->input('id'));
        // Odstranění všech překladů pro daný NavigationItem
        foreach ($NavigationItem->translations as $translation) {
            $translation->delete();
        }
        $NavigationItem->delete();


        Alert::info(__('Navigation item was removed'));

        return back();
    }

    public function asyncloadMenuItemData(Request $request, $id)
    {
        $NavigationItem = NavigationItem::findOrFail($id);
        // Najděte překlad pro daný jazyk, například 'cs'
        $translation = $NavigationItem->translations->firstWhere('locale', 'cs');

        return [
            'menuCategoryItem' => [
                'name' => $translation ? $translation->title : '',
                'url' => $NavigationItem->url,
            ]
        ];
    }



    public function updateMenuItem(Request $request, $id)
    {
        //  dd($request, $id);
        $request->validate([
            'menuCategoryItem.name' => 'required|string',
            'menuCategoryItem.url' => 'required|string',
        ]);

        $NavigationItem = NavigationItem::with('translations')->findOrFail($id);

        // Aktualizace NavigationItem
        $NavigationItem->url = $request['menuCategoryItem']['url'];
        $NavigationItem->save();

        // Najděte překlad pro daný jazyk, například 'cs'
        $translation = $NavigationItem->translations->firstWhere('locale', 'cs');

        // Pokud překlad existuje, aktualizujte ho
        if ($translation) {
            $translation->title = $request['menuCategoryItem']['name'];
            $translation->save();
        } else {
            // Pokud překlad neexistuje, vytvořte nový
            $translation = new NavigationItemTranslation();
            $translation->title = $request['menuCategoryItem']['name'];
            $translation->locale = 'cs';
            $translation->navigation_item_id = $NavigationItem->id;
            $translation->save();
        }

        Alert::success('Položka byla úspěšně upravena.');

        return back();
    }
}
