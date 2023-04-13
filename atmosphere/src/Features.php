<?php

namespace Modules\Atmosphere;

class Features
{
    /**
     * Determine if the given feature is enabled.
     *
     * @param  string  $feature
     * @return bool
     */
    public static function enabled(string $feature)
    {
        return in_array($feature, config('atmosphere.features', []));
    }

    /**
     * Determine if the feature is enabled and has a given option enabled.
     *
     * @param  string  $feature
     * @param  string  $option
     * @return bool
     */
    public static function optionEnabled(string $feature, string $option)
    {
        return static::enabled($feature) &&
               config("atmosphere-options.{$feature}.{$option}") === true;
    }

    /**
     * Determine if the application is allowing profile photo uploads.
     *
     * @return bool
     */
    public static function hasAdminPanelFeatures()
    {
        return static::enabled(static::adminPanel());
    }


    /**
     * Determine if the application is using any team features.
     *
     * @return bool
     */
    public static function hasTeamLazyFeatures()
    {
        return static::enabled(static::lazyTeams());
    }

    /**
     * Enable the admin template feature.
     *
     * @return string
     */
    public static function adminPanel()
    {
        return 'admin-panel';
    }

    /**
     * Enable the lazy teams feature.
     *
     * @param  array  $options
     * @return string
     */
    public static function lazyTeams(array $options = [])
    {
        if (! empty($options)) {
            config(['atmosphere-options.lazyTeams' => $options]);
        }

        return 'lazy-teams';
    }
}
