<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Schema;

class PostService
{
    /**
     * Service Model
     *
     * @var Model
     */
    public $model;

    /**
     * Pagination
     *
     * @var integer
     */
    public $pagination;

    /**
     * Service Constructor
     *
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        $this->model = $post;
        $this->pagination = 25;
    }

    /**
     * All Model Items
     *
     * @return array
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Paginated items
     *
     * @return LengthAwarePaginator
     */
    public function paginated()
    {
        return $this->model->paginate($this->pagination);
    }

    /**
     * Search the model
     *
     * @param  mixed $payload
     * @return LengthAwarePaginator
     */
    public function search($payload)
    {
        $query = $this->model->orderBy('created_at', 'desc');
        $query->where($this->model->primaryKey, 'LIKE', '%'.$payload.'%');

        $columns = Schema::getColumnListing('posts');

        foreach ($columns as $attribute) {
            $query->orWhere($attribute, 'LIKE', '%'.$payload.'%');
        };

        return $query->paginate($this->pagination)->appends([
            'search' => $payload
        ]);
    }

    /**
     * Create the model item
     *
     * @param  array $payload
     * @return Model
     */
    public function create($payload)
    {
        return $this->model->create($payload);
    }

    /**
     * Find Model by ID
     *
     * @param  integer $id
     * @return Model
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * Model update
     *
     * @param  integer $id
     * @param  array $payload
     * @return Model
     */
    public function update($id, $payload)
    {
        return $this->find($id)->update($payload);
    }

    /**
     * Destroy the model
     *
     * @param  integer $id
     * @return bool
     */
    public function destroy($id)
    {
        return $this->model->destroy($id);
    }
}
