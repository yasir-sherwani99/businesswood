<?php

namespace Corals\Modules\CMS\Policies;

use Corals\User\Models\User;
use Corals\Modules\CMS\Models\Testimonial;

class TestimonialPolicy
{

    /**
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        if ($user->can('CMS::testimonial.view')) {
            return true;
        }
        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('CMS::testimonial.create');
    }

    /**
     * @param User $user
     * @param Testimonial $testimonial
     * @return bool
     */
    public function update(User $user, Testimonial $testimonial)
    {
        if ($user->can('CMS::testimonial.update')) {
            return true;
        }
        return false;
    }

    /**
     * @param User $user
     * @param Testimonial $testimonial
     * @return bool
     */
    public function destroy(User $user, Testimonial $testimonial)
    {
        if ($user->can('CMS::testimonial.delete')) {
            return true;
        }
        return false;
    }


    /**
     * @param $user
     * @param $ability
     * @return bool
     */
    public function before($user, $ability)
    {
        if ($user->hasPermissionTo('Administrations::admin.cms')) {

            return true;
        }

        return null;
    }
}
