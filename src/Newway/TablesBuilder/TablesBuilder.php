<?php namespace Newway\TablesBuilder;

use Lang;

/**
 * Class TablesBuilder
 * Generate table html
 *
 * @package App\Classes\TablesBuilder
 */
class TablesBuilder
{
    protected $tableAttr = [];
    protected $headColumns = [];
    protected $footColumns = [];
    protected $headRowAttr = [];
    protected $footRowAttr = [];

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * create new TablesBuilder instance
     *
     * @param array $attr
     * @return static
     */
    public static function create(array $attr = [])
    {
        $ins = new static;
        $ins->tableAttr = $attr;
        return $ins;
    }

    /**
     * Push array of columns here
     * example addHead([ ['text' => 'col1'], ['text' => 'col2', 'attr' => ['class' => 'myClass']], [] ])
     * first - only text
     * second - text and attributes
     * third - empty <td></td>
     *
     * @param string $section
     * @param array $columns
     * @return $this
     */
    protected  function addSection($section, array $columns)
    {
        foreach($columns as $column)
            $this->addColumnInSection(
                $section,
                !empty($column['text']) ? $column['text'] : '',
                !empty($column['attr']) ? $column['attr'] : []
            );
        return $this;
    }

    /**
     * Add attributes to header or footer <tr>
     *
     * @param $section
     * @param array $attr
     * @return $this
     */
    protected function addAttrToSection($section, array $attr)
    {
        $this->{"{$section}RowAttr"} = $attr;
        return $this;
    }

    /**
     * Add one header or footer column
     * @param $section
     * @param $text
     * @param array $attr
     * @return $this
     */
    protected function addColumnInSection($section, $text, array $attr = [])
    {
        $this->{"{$section}Columns"}[] = [
            'attr' => $attr,
            'text' => $text,
        ];
        return $this;
    }

    /**
     * @param bool $initDatatable
     * @param bool $useLaravelLang
     * @return string
     */
    public function make($initDatatable = true)
    {
        $html = '<table ' . $this->attributes($this->tableAttr) . '>';
        $html .= $this->getSection('head');
//        $html .= $this->getBody(); need to finish
        $html .= $this->getSection('foot');
        $html .= '</table>';
        $translations = '

        ';
        if($initDatatable && $id = $this->tableAttr['id'])
            $html .= '<script>$(document).ready(function () {
                var t = $("#' . $id . '").DataTable({
                    sPaginationType: "bootstrap_alt",
                    bProcessing: !0,
                    bServerSide: !0,
                    ajax: "",
                    sAjaxSource: "",
                    stateSave: true,
                    "language": {
                        "lengthMenu": "' . Lang::trans('tables_builder::datatables.lengthMenu') . '",
                        "zeroRecords": "' . Lang::trans('tables_builder::datatables.zeroRecords') . '",
                        "info": "' . Lang::trans('tables_builder::datatables.info') . '",
                        "infoEmpty": "' . Lang::trans('tables_builder::datatables.infoEmpty') . '",
                        "search": "' . Lang::trans('tables_builder::datatables.search') . '",
                        "infoFiltered": "' . Lang::trans('tables_builder::datatables.infoFiltered') . '",
                        "paginate": {
                            "first": "' . Lang::trans('tables_builder::datatables.paginate.first') . '",
                            "last": "' . Lang::trans('tables_builder::datatables.paginate.last') . '",
                            "next": "' . Lang::trans('tables_builder::datatables.paginate.next') . '",
                            "previous": "' . Lang::trans('tables_builder::datatables.paginate.previous') . '"
                        }
                    },
                    columnDefs: [{targets: "_all", defaultContent: ""}],
                        fnDrawCallback: function () {
                        return initToggles()
                    }
                });
                t.columns().each(function (e) {
                  var footer = t.column(e).footer();
                  if(footer != null) {
                      return $("select", t.column(e).footer()).on("keyup change", function () {
                        return t.column(e).search(this.value).draw()
                      })
                  }
                });
              })</script>';
        return $html;
    }

    /**
     * Generate table header
     * @param string $section
     * @return string
     */
    private function getSection($section)
    {
        $html = '';
        $sectionArrayName = "{$section}Columns";
        if (count((array)$this->$sectionArrayName) > 0) {
            $html .= "<t$section><tr {$this->attributes($this->{"{$section}RowAttr"})}>";
            foreach ($this->$sectionArrayName as $col) {
                $html .= "<th {$this->attributes($col['attr'])}>{$col['text']}</th>";
            }
            $html .= "</tr></t$section>";
        }
        return $html;
    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @param  array $attributes
     * @return string
     */
    public function attributes($attributes)
    {
        $html = array();

        // For numeric keys we will assume that the key and the value are the same
        // as this will convert HTML attributes such as "required" to a correct
        // form like required="required" instead of using incorrect numerics.
        foreach ((array)$attributes as $key => $value) {
            $element = $this->getAttributeExpression($key, $value);
            if ($element) {
                $html[] = $element;
            }
        }

        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }

    /**
     * Build a single attribute expression element.
     *
     * @param  string $key
     * @param  string $value
     * @return string
     */
    private function getAttributeExpression($key, $value)
    {
        if (is_numeric($key)) {
            $key = $value;
        }
        if (!is_null($value)) {
            return $key . '="' . e($value) . '"';
        }
        return false;
    }

    /**
     * @param $name
     * @param array $arguments
     * @throws \Exception
     */
    public function __call($name, array $arguments) {
        if(preg_match('/^add(Head|Foot)Column$/', $name, $matches)) {
            return $this->addColumnInSection(strtolower($matches[1]), $arguments[0], $arguments[1]);
        }
        if(preg_match('/^add(Head|Foot)Attr/', $name, $matches)) {
            return $this->addAttrToSection(strtolower($matches[1]), $arguments[0], $arguments[1]);
        }
        if(preg_match('/^add(Head|Foot)/', $name, $matches)) {
            return $this->addSection(strtolower($matches[1]), $arguments[0]);
        }

        throw new \Exception("Method $name not found in class " . __CLASS__);
    }

}