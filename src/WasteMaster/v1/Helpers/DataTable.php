<?php

namespace WasteMaster\v1\Helpers;

use Illuminate\Database\Eloquent\Collection;

/**
 * Class DataTable
 *
 * A simple, server-side datatable solution that allows for quickly building
 * out tables for admin areas that are paginated, searchable, and can be
 * sorted by any column in the table.
 *
 * Designed for use with a Boostrap 3 theme at the moment.
 *
 * @package Vault
 */
class DataTable
{
    /**
     * The model that should be used for data
     * @var \Eloquent
     */
    protected $model;

    /**
     * Amount of results per page
     * @var int
     */
    protected $perPage = 20;

    /**
     * The column to sort the results by
     * @var string
     */
    protected $sortColumn;

    /**
     * Which direction to sort.
     * @var string
     */
    protected $sortDirection = 'asc';

    protected $alwaysSortColumn;
    protected $alwaysSortDirection;

    /**
     * The resulting Collection
     * @var Collection
     */
    protected $results;

    /**
     * The columns to display on the table.
     * @var array
     */
    protected $columns = [];

    /**
     * The columns that can be searched on.
     * @var array
     */
    protected $searchable = [];

    /**
     * The URL that is used for both the
     * search form and the sortable fields.
     * If blank, current url will be used.
     * @var string
     */
    protected $url;

    protected $joins = [];
    protected $wheres = [];
    protected $select;
    protected $with;

    protected $eagerLoad;

    protected $hideColumns = [];

    /**
     * The
     * @var null
     */
    protected $searchTerm;

    //--------------------------------------------------------------------

    public function __construct($model)
    {
        $this->model = $model;

        $this->url = url()->current();

        // Sorting
        $this->sortColumn    = $_GET['sort'] ?? null;
        $this->sortDirection = $_GET['sort_dir'] ?? 'asc';

        // Search
        $this->searchTerm = $_POST['search'] ?? $_GET['search'] ?? null;
    }

    //--------------------------------------------------------------------

    /**
     * Generates the results and gets the library ready to display results.
     * This should be the final method called within the controller after
     * the setup methods have been defined.
     *
     * @param int|null $perPage
     *
     * @return \Vault\DataTable
     */
    public function prepare(int $perPage = null): self
    {
        if(is_integer($perPage))
        {
            $this->perPage = $perPage;
        }

        if(!empty($this->searchTerm))
        {
            // Since we can search multiple columns,
            // we need to group them together so they play
            // nice with any other wheres.
            $this->model = $this->model->where(function($query) {
                foreach ($this->searchable as $column)
                {
                    $shortColumn = trim(substr($column, strpos($column, '.')), '.');

                    if ($shortColumn == 'id' && is_numeric($this->searchTerm))
                    {
                        $query = $query->orWhere($column, $this->searchTerm);
                    }
                    else
                    {
                        $query = $query->orWhere($column, 'like', '%'.$this->searchTerm.'%');
                    }
                }
            });
        }

        if (isset($_GET['lead']) && is_numeric($_GET['lead']))
        {
            $this->model = $this->model->where('lead_id', (int)$_GET['lead']);
        }

        if(count($this->joins))
        {
            foreach ($this->joins as $join)
            {
                $this->model = $this->model->join(...$join);
            }
        }

        if(count($this->wheres))
        {
            foreach ($this->wheres as $where)
            {
                $this->model = $this->model->where(...$where);
            }
        }

        if ($this->eagerLoad !== null)
        {
            $this->model = $this->model->with(...$this->eagerLoad);
        }

        if (! empty($this->select) && is_array($this->select))
        {
            $this->model = $this->model->select(...$this->select);
        }

        if (! empty($this->alwaysSortColumn))
        {
            $this->results = $this->model->orderBy($this->alwaysSortColumn, $this->alwaysSortDirection);
        }

        if(!empty($this->sortColumn))
        {
            $this->results = $this->model->orderBy($this->sortColumn, $this->sortDirection)
                                                                         ->paginate($this->perPage);
        }
        else
        {
            $this->results = $this->model->paginate($this->perPage);
        }

        return $this;
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Setup
    //--------------------------------------------------------------------

    /**
     * Set the columns that should have a "hidden-xs" class added.
     *
     * @param array $columns
     *
     * @return $this
     */
    public function hideOnMobile(array $columns)
    {
        $this->hideColumns = $columns;

        return $this;
    }


    /**
     * Force a table join. This would be the same as those in Query Builder.
     *
     * @param array ...$params
     *
     * @return $this
     */
    public function join(...$params)
    {
        $this->joins[] = $params;

        return $this;
    }

    //--------------------------------------------------------------------

    public function where(...$params)
    {
        $this->wheres[] = $params;

        return $this;
    }

    //--------------------------------------------------------------------

    public function select(...$params)
    {
        $this->select = $params;

        return $this;
    }

    /**
     * Sets the default sort value to use if nothing else has
     * been specified in the Query values.
     *
     * @param string $field
     * @param string $dir
     *
     * @return $this
     */
    public function setDefaultSort(string $field, string $dir='asc')
    {
        $this->sortColumn = $_GET['sort'] ?? $field;
        $this->sortDirection = $_GET['sort_dir'] ?? $dir;

        return $this;
    }

    //--------------------------------------------------------------------

    public function setAlwaysSort(string $field, string $dir='asc')
    {
        $this->alwaysSortColumn = $field;
        $this->alwaysSortDirection = $dir;

        return $this;
    }


    /**
     * Sets the URL used by both the search form and the
     * the sortable links. By default, the library uses the
     * current url.
     *
     * @param string $url
     *
     * @return \Vault\DataTable
     */
    public function setURL(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Sets the columns that should be shown on the result table.
     * The fields should be set in the order you wish them to be
     * displayed.
     *
     * @param array
     *
     * @return \Vault\DataTable
     */
    public function showColumns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * The column names that can be searched. Columns are searched
     * with a like% query against those tables, so the results will
     * always start with the searched term.
     *
     * Example:
     * 	$this->searchColumns(['title', 'description']);
     *
     * @param array $columns
     *
     * @return \Vault\DataTable
     */
    public function searchColumns(array $columns): self
    {
        $this->searchable = $columns;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Sets the amount of rows that should be returned for each page.
     *
     * @param int $amt
     *
     * @return \Vault\DataTable
     */
    public function perPage(int $amt): self
    {
        $this->perPage = $amt;

        return $this;
    }

    //--------------------------------------------------------------------


    //--------------------------------------------------------------------
    // Results
    //--------------------------------------------------------------------

    /**
     * Returns whether there are any results to display.
     *
     * @return bool
     */
    public function hasResults(): bool
    {
        return is_null($this->results)
                    ? false
                    : (bool)$this->results->count();
    }

    //--------------------------------------------------------------------

    /**
     * Returns the current result collection.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function rows()
    {
        return $this->results;
    }

    //--------------------------------------------------------------------

    /**
     * Returns the model that's in use.
     *
     * @return \Eloquent
     */
    public function model()
    {
        return $this->model;
    }

    //--------------------------------------------------------------------

    /**
     * If set, will pass this string to the with() method on the model.
     *
     * @param array $children
     *
     * @return $this
     */
    public function eagerLoad(...$children)
    {
        $this->eagerLoad = $children;

        return $this;
    }


    //--------------------------------------------------------------------
    // Rendering
    //--------------------------------------------------------------------

    /**
     * Renders our the search input field, with current search
     * term, etc.
     */
    public function renderSearch(): string
    {
        $output  = "<form action='{$this->url}' method='post' class='form-inline'>\n";
        $output .= csrf_field();

        $output .= "<div class='input-group'>\n";
        $output .= "<input type='search' name='search' class='form-control' value='{$this->searchTerm}' placeholder='Search...'>";
        $output .= "<span class=\"input-group-addon\"><i class='fa fa-search'></i>\n</div>\n";

        $output .= "</form>\n";

        return $output;
    }

    //--------------------------------------------------------------------

    /**
     * Renders out the "meta" information about the result set,
     * something like 1-20 of 1345.
     */
    public function renderMeta(): string
    {
        $output = '<span>'. $this->results->firstItem() .'</span> - '
                . '<span>'. $this->results->lastItem() .'</span> of '
                . '<span>'. $this->results->total() .'</span>';

        return $output;
    }

    //--------------------------------------------------------------------

    /**
     * Renders the table header with links for sorting
     * columns.
     *
     * @param string $class
     * @param string $id
     *
     * @return string
     * @internal param $
     *
     */
    public function renderHeader(): string
    {
        $output = "<thead>\n<tr>\n";

        $columns = count($this->columns)
            ? $this->columns
            : $this->model->getFillable();

        foreach ($columns as $key => $value)
        {
            $column = is_numeric($key) ? $value : $key;

            $query = [
                'sort' => $column,
                'sort_dir' => $this->sortDirection == 'asc' ? 'desc' : 'asc',
            ];

            if (! empty($this->searchTerm))
            {
                $query['search'] = $this->searchTerm;
            }

            if (! empty($_GET['lead']))
            {
                $query['lead'] = $_GET['lead'];
            }

            $sort = http_build_query($query);

            $link = "<a href='{$this->url}?{$sort}'>{$value}</a>";

            if ($column == $this->sortColumn)
            {
                $arrow = $this->sortDirection == 'asc'
                        ? "<span class='glyphicon glyphicon-menu-down'></span>"
                        : "<span class='glyphicon glyphicon-menu-up'></span>";

                $link = "<a href='{$this->url}?{$sort}'>{$value} {$arrow}</a>";
            }

            $hide = in_array($key, $this->hideColumns)
                ? ' class="hidden-xs"'
                : '';

            $output .= "<th{$hide}>{$link}</th>\n";
        }

        $output .="<th>Actions</th>\n";

        $output .= "</tr>\n</thead>";

        return $output;
    }

    //--------------------------------------------------------------------

    /**
     * Renders the table footer.
     *
     * @return string
     */
    public function renderFooter(): string
    {
    }

    //--------------------------------------------------------------------

    /**
     * Renders the pagination links
     *
     * @return string
     */
    public function renderLinks(): string
    {
        if (! empty($this->sortColumn))
        {
            return $this->results->appends([
                'sort'   => $this->sortColumn,
                'dir'    => $this->sortDirection,
                'search' => $this->searchTerm
            ])->links();
        }

        return $this->results->links();
    }

    //--------------------------------------------------------------------

}
