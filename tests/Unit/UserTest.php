<?php

namespace Tests\Unit;


use App\User;
use Facades\Tests\Setup\ProjectFactory;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;


class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
     use RefreshDatabase;

    public function test_a_user_has_projects(){
        $this->withoutExceptionHandling();
        $user = factory('App\User')->create();
        $this->assertInstanceOf(Collection::class, $user->projects);
    }

     function test_a_user_has_accessible_projects(){
        $john = $this->signIn();

        ProjectFactory::ownedBy($john)->create();

        $this->assertCount(1, $john->accessibleProjects());

        $sally = factory(User::class)->create();
        $nick = factory(User::class)->create();

        $project = tap(ProjectFactory::ownedBy($sally)->create())->invite($nick);

        $this->assertCount(1, $john->accessibleProjects());

        $project->invite($john);

        $this->assertCount(2, $john->accessibleProjects());
     }
}
