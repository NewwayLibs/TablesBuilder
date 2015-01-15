<?php namespace App\Helpers\TablesBuilder;

/**
 * Class TablesBuilder
 * Generate table html
 *
 * @package App\Classes\TablesBuilder
 */
class TablesBuilder
{
    private $tableAttr = [];
    private $headColumns = [];
    private $footColumns = [];
    private $headRowAttr = [];
    private $footRowAttr = [];

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
     * @param array $columns
     * @return $this
     */
    public function addHead(array $columns)
    {
        $this->headColumns = $columns;
        return $this;
    }

    /**
     * Push array of columns here
     *
     * @param array $columns
     * @return $this
     */
    public function addFoot(array $columns)
    {
        $this->footColumns = $columns;
        return $this;
    }

    /**
     * Add attributes to head <tr>
     *
     * @param array $attr
     * @return $this
     */
    public function addHeadAttr(array $attr)
    {
        $this->headRowAttr = $attr;
        return $this;
    }

    /**
     * Add attributes to foot <tr>
     *
     * @param array $attr
     * @return $this
     */
    public function addFootAttr(array $attr)
    {
        $this->footRowAttr = $attr;
        return $this;
    }

    /**
     * Add one head column
     *
     * @param $text
     * @param array $attr
     * @return $this
     */
    public function addHeadColumn($text, array $attr = [])
    {
        $this->headColumns[] = [
            'attr' => $attr,
            'text' => $text,
        ];
        return $this;
    }

    /**
     * Add one footer column
     *
     * @param $text
     * @param array $attr
     * @return $this
     */
    public function addFootColumn($text, array $attr = [])
    {
        $this->footColumns[] = [
            'attr' => $attr,
            'text' => $text,
        ];
        return $this;
    }

    /**
     * @param bool $initDatatable
     * @return string
     */
    public function make($initDatatable = true)
    {
        $html = '<table ' . $this->attributes($this->tableAttr) . '>';
        $html .= $this->getHead();
//        $html .= $this->getBody(); need to finish
        $html .= $this->getFoot();
        $html .= '</table>';
        if($initDatatable && $id = $this->tableAttr['id'])
            $html .= '<script>$(document).ready(function () {
                var t = $("#' . $id . '").DataTable({
                  sPaginationType: "bootstrap_alt",
                  bProcessing: !0,
                  bServerSide: !0,
                  ajax: "",
                  sAjaxSource: "",
                  columnDefs: [{targets: "_all", defaultContent: ""}],
                  fnDrawCallback: function () {
                    return initToggles()
                  }
                });
                t.columns().eq(0).each(function (e) {
                  return $("select", t.column(e).footer()).on("keyup change", function () {
                    return t.column(e).search(this.value).draw()
                  })
                });
              })</script>';
        return $html;
    }

    /**
     * Generate table header
     *
     * @return string
     */
    private function getHead()
    {
        $thead = '';
        if (count((array)$this->headColumns) > 0) {
            $thead .= '<thead><tr' . $this->attributes($this->headRowAttr) . '>';
            foreach ($this->headColumns as $col) {
                $thead .= '<th ' . $this->attributes($col['attr']) . '>' . $col['text'] . '</th>';
            }
            $thead .= '</tr></thead>';
        }
        return $thead;
    }

    /**
     * Generate table footer
     *
     * @return string
     */
    private function getFoot()
    {
        $tfoot = '';
        if (count((array)$this->headColumns) > 0) {
            $tfoot .= '<tfoot><tr ' . $this->attributes($this->footRowAttr) . '>';
            foreach ($this->footColumns as $col) {
                $tfoot .= '<th ' . $this->attributes($col['attr']) . '>' . $col['text'] . '</th>';
            }
            $tfoot .= '</tr></tfoot>';
        }
        return $tfoot;
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

}