<?php

namespace Corals\Settings\Http\Controllers;

use Corals\Foundation\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class UtilitiesController extends BaseController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * {!! CoralsForm::select('users','Users', [], false, null,
     * ['class'=>'select2-ajax','data'=>[
     * 'model'=>\Corals\User\Models\User::class,
     * 'columns'=> json_encode(['name','email']),
     * 'selected'=>json_encode([1=>'zzzzzz',3=>'xxxxxxxxx']),
     * 'orWhere'=>json_encode([]),
     * 'where'=>json_encode([
     * ['field'=>'tableX.col_x','operation'=>'=','value'=>'xx'],
     * ['field'=>'tableX.col_y','operation'=>'=','value'=>'yy']
     * ]),
     * 'join' =>[
     * 'table'=>'tableX',
     * 'type'=>'leftJoin',
     * 'on' =>['tableX.user_id','users.id']
     * ]
     * ]],'select2')
     * !!}
     */
    public function select2(Request $request)
    {
        if (!$request->get('columns') || !$request->get('model')) {
            return response()->json([]);
        }

        $query = $request->get('query');
        $columns = $request->get('columns');
        $textColumns = $request->get('textColumns');
        $model = $request->get('model');
        $selected = $request->get('selected', []);
        $where = $request->get('where', []);
        $orWhere = $request->get('orWhere', []);
        $resultMapper = $request->get('resultMapper', []);
        $join = $request->get('join', []);

        $model_table = with(new $model)->getTable();

        $result = null;

        if (empty($query) && empty($selected)) {
            return response()->json([]);
        }

        //check if table name is set or not
        $columns = array_map(function ($column) use ($model_table) {
            $column = strpos($column, '.') !== false ? $column : ($model_table . '.' . $column);
            return $column;
        }, $columns);

        if (empty($textColumns)) {
            $textColumns = $columns;
        } else {
            $textColumns = array_map(function ($column) use ($model_table) {
                $column = strpos($column, '.') !== false ? $column : ($model_table . '.' . $column);
                return $column;
            }, $textColumns);
        }

        $result = $model::where(function ($q) use ($columns, $query, $model_table) {
            foreach ($columns as $index => $column) {
                $q = $q->orWhere("$column", 'like', '%' . $query . '%');
            }
        });

        if (!empty($selected)) {
            $result = $result->whereIn($model_table . '.id', $selected);
        }

        if (!empty($where)) {
            $result = $this->applyConditions($where, $result);
        }

        if (!empty($orWhere)) {
            $result = $result->where(function ($query) use ($orWhere) {
                return $query = $this->applyConditions($orWhere, $query, 'orWhere');
            });
        }


        if (!empty($join)) {
            $result = $result->{$join['type']}($join['table'], $join['on'][0], $join['on'][1]);
        }

        $queryClass = strtolower(class_basename($model));
        $scopes = [];
        $scopes = \Filters::do_filter('select_scopes_' . $queryClass, $scopes, $queryClass);

        foreach ($scopes as $scope) {
            $scope->apply($result);
        }


        $sep = "";
        $text = "";
        foreach ($textColumns as $textColumn) {
            $text .= $sep . $textColumn;
            $sep = ',';
        }

        $id = $model_table . '.id as id';

        $result->select(\DB::raw("CONCAT_WS(' - ', $text) as text"), $id);

        $result = $result->limit(200)->distinct()->get();

        $results = [];

        foreach ($result as $item) {
            array_push($results, ['id' => $item->id, 'text' => $item->text]);
        }

        if (!empty($resultMapper)) {
            $results = call_user_func($resultMapper, $results);
        }

        return response()->json($results);
    }

    protected function applyConditions($conditions, $query, $where = 'where')
    {
        foreach ($conditions as $w) {
            switch ($w['operation']) {
                case 'in':
                    $query = $query->{$where . 'In'}($w['field'], $w['value']);
                    break;
                case 'not_in':
                    $query = $query->{$where . 'NotIn'}($w['field'], $w['value']);
                    break;
                case 'is_null':
                    $query = $query->{$where . 'Null'}($w['field']);
                    break;
                case 'not_null':
                    $query = $query->{$where . 'NotNull'}($w['field']);
                    break;
                default:
                    $query = $query->{$where}($w['field'], $w['operation'], $w['value']);
            }
        }

        return $query;
    }
}
