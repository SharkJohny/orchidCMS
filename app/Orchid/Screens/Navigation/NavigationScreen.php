<?php

namespace App\Orchid\Screens\Navigation;


use Orchid\Screen\TD;
use Orchid\Screen\Screen;
use App\Models\Navigation;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Actions\DropDown;

class NavigationScreen extends Screen
{

    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Navigace';

    /**
     * Display header description.
     *
     * @var string|null
     */
    public $description = 'Manage your site navigations';
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'navigations' => Navigation::all(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Navigace';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Vytvořit Menu')
                ->modal('CreateMenu')
                ->method('saveMenu')
                ->icon('plus'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('navigations', [
                TD::make('name', __('Name'))
                    ->sort()
                    ->filter(TD::FILTER_TEXT)
                    ->render(function ($navigation) {
                        return "<a href='" . route('platform.navigation.edit', $navigation->id) . "'>{$navigation->name}</a>";
                    }),
                TD::make(__('Actions'))
                    ->align(TD::ALIGN_CENTER)
                    ->width('200px')
                    ->render(function ($navigation) {
                        return Dropdown::make(__('Actions'))
                            ->icon('options-vertical')
                            ->list([
                                Link::make(__('Edit'))
                                    ->route('platform.navigation.edit', $navigation->id)
                                    ->icon('pencil')
                                    ->parameters(['id' => $navigation->id]),

                                Button::make(__('Delete'))
                                    ->method('remove')
                                    ->icon('trash')
                                    ->confirm(__('Are you sure you want to delete this item?'))
                                    ->parameters(['id' => $navigation->id]),

                                //   return $editButton->render() . $deleteButton->render(),
                            ])->alignRight()
                            ->render();
                    }),

            ]),
            Layout::modal('CreateMenu', [
                Layout::rows([
                    Input::make('menuCategory.name')
                        ->title('Nazev Menu')
                        ->placeholder('Nazev Menu')
                        ->required(),


                ])
            ])->title('Vytvorit Kategorii')
                ->method('saveMenu')
                ->applyButton('Přidat'),


        ];
    }


    public function saveMenu(Request $request)
    {
        $request->validate([
            'menuCategory.name' => 'required|string',
        ]);

        $category = new Navigation();
        $category->name = $request['menuCategory']['name'];

        $category->save();
        Alert::success('Položka byla úspěšně uložena.');
        return back();
    }
}
