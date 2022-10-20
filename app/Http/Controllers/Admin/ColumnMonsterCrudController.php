<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ColumnMonsterCrudController.
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ColumnMonsterCrudController extends MonsterCrudController
{
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Monster::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/column-monster');
        CRUD::setEntityNameStrings('column monster', 'column monsters');
        $this->crud->set('show.setFromDb', false);
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     *
     * @return void
     */
    public function setupListOperation()
    {
        $this->crud->disableResponsiveTable();

        $timeSpaceColumns = static::getFieldsArrayForTimeAndSpaceTab();
        // Changed "time_range" column definition (changed "name" in comma separeated & convert "name" key in column definition)
        if ($timeSpaceColumns) {
            foreach ($timeSpaceColumns as $columnKey => $timeSpaceColumn) {
                if ($timeSpaceColumn['type'] == 'date_range') {
                    unset($timeSpaceColumns[$columnKey]);

                    // Creating new variable array to over-ride date_range column as that is "unset" above
                    $timeSpaceColumnDateRange = array('name' => 'start_date,end_date', 'label' => $timeSpaceColumn['label'], 'type' => $timeSpaceColumn['type']);
                    $timeSpaceColumns[$columnKey] = $timeSpaceColumnDateRange;
                }
            }
        }

        $relationshipColumns = static::getFieldsArrayForRelationshipsTab();
        // Removing "custom_html" column definition
        if ($relationshipColumns) {
            foreach ($relationshipColumns as $columnKey => $relationshipColumn) {
                if (isset($relationshipColumn['type']) && ($relationshipColumn['type'] == 'custom_html')) {
                    unset($relationshipColumns[$columnKey]);
                }
            }
        }

        $this->crud->addColumns(static::getFieldsArrayForSimpleTab());
        $this->crud->addColumns($timeSpaceColumns);
        $this->crud->addColumns(static::getFieldsArrayForSelectsTab());
        $this->crud->addColumns($relationshipColumns);
        $this->crud->addColumns(static::getFieldsArrayForUploadsTab());
        $this->crud->addColumns(static::getFieldsArrayForWysiwygEditorsTab());
        $this->crud->addColumns(static::getFieldsArrayForMiscellaneousTab());

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);.
         */
    }
}