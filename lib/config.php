<?php
namespace Podlove\Beta;

class Config
{

    public static function data()
    {
        $empty_data = ((object) ['plugins' => []]);

        // "podlove-beta-tester": {
        //       "github": "podlove/podlove-beta-tester",
        //     "hidden": true,
        //     "title": "Podlove Beta Tester",
        //     "file" : "podlove-beta-tester/podlove-beta-tester.php",
        //     "branches": {
        //         "master": {}
        //     }
        // },

        $config = '
{
    "plugins": {
        "podlove-podcasting-plugin-for-wordpress": {
			"github": "podlove/podlove-publisher",
            "title": "Podlove Podcast Publisher",
            "file" : "podlove-podcasting-plugin-for-wordpress/podlove.php",
            "branches": {
                "beta": {
                    "state": "beta",
                    "description": "Work in progress of the next release"
                }
            }
        }
    }
}
';

        if ($config === false) {
            $config = $empty_data;
        } else {
            $config = json_decode($config);
        }

        return $config;
    }

    public static function plugin_slug_for_filename($filename)
    {
        $plugin_data = self::data()->plugins;
        foreach ($plugin_data as $slug => $plugin) {
            if ($plugin->file === $filename) {
                return $slug;
            }
        }

        return null;
    }

    public function plugins()
    {
        $plugin_data = self::data()->plugins;
        $plugins     = [];

        foreach ($plugin_data as $plugin_slug => $plugin) {
            $plugins[] = new Plugin($plugin_slug, $plugin);
        }

        return $plugins;
    }

    public function plugin($slug)
    {
        $plugin_data = self::data()->plugins;

        if (isset($plugin_data->$slug)) {
            return new Plugin($slug, $plugin_data->$slug);
        }

        return null;
    }

}
