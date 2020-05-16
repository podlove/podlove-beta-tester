<?php
namespace Podlove\Beta;

class Plugin
{

    public $slug;
    public $title;
    public $file;
    public $hidden;
    public $github;

    private $branches;

    public function __construct($slug, $data)
    {
        $this->slug   = $slug;
        $this->title  = $data->title;
        $this->file   = $data->file;
        $this->github = $data->github;
        $this->hidden = isset($data->hidden) ? (bool) $data->hidden : false;

        $this->branches = [];
        foreach ($data->branches as $branch_slug => $branch) {
            $this->branches[] = new Branch(
                $branch_slug,
                isset($branch->state) ? $branch->state : 'alpha',
                isset($branch->description) ? $branch->description : ''
            );
        }
    }

    public function branches()
    {
        return $this->branches;
    }

    public function absolute_file_path()
    {
        return trailingslashit(wp_normalize_path(WP_PLUGIN_DIR)) . $this->file;
    }

    public function absolute_dir_path()
    {
        return dirname($this->absolute_file_path());
    }

}
