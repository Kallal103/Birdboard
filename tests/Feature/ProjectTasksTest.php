<?php

namespace Tests\Feature;

use App\Project;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Facades\Tests\Setup\ProjectFactory;
use Tests\TestCase;


class ProjectTasksTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_add_tasks_to_projects()
    {
        $project = factory('App\Project')->create();

        $this->post($project->path() . '/tasks')->assertRedirect('login');
    }

    /** @test */
    function test_only_the_owner_of_a_project_may_add_tasks()
    {
        $this->signIn();

        $project = factory('App\Project')->create();

        $this->post($project->path() . '/tasks', ['body' => 'Test task'])
            ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', ['body' => 'Test task']);
    }  
    
    function test_only_the_owner_of_a_project_may_update_a_task()
    {
        $this->signIn();
        
        $project = ProjectFactory::withTasks(1)->create();
        //$project = factory('App\Project')->create();

        //$task = $project->addTask('test task');

        $this->patch($project->tasks[0]->path(), ['body' => 'changed'])
            ->assertStatus(403);

       

            $this->assertDatabaseMissing('tasks', ['body' => 'changed']);
    }

    public function test_a_project_can_have_tasks(){

       // $this->withoutExceptionHandling();

        // $this->signIn();
      
        // $project = auth()->user()->projects()->create(
        //     factory(Project::class)->raw()
        // );
        //$project = factory(Project::class)->create(['owner_id' => auth()->id()]);

        $project = ProjectFactory::create();

        $this->actingAs($project->owner)
        ->post($project->path().'/tasks', ['body'=>'Test Task']);

        $this->get($project->path())
        ->assertSee('Test Task');

    }

    public function test_a_task_can_be_updated(){
        //$this->withoutExceptionHandling();
        
        $project = ProjectFactory::withTasks(1)->create();

        //$this->signIn();
      
        // $project= auth()->user()->projects()->create(
        //     factory(Project::class)->raw()
        // );

        // $task = $project->addTask('test task');

        $this->actingAs($project->owner)
        ->patch($project->tasks->first()->path(),[
            'body' => 'changed',
            
        ]);

        $this->assertDatabaseHas('tasks',[
            'body' => 'changed',
          
        ]);
    }   
    
    public function test_a_task_can_be_completed(){
        //$this->withoutExceptionHandling();
        
        $project = ProjectFactory::withTasks(1)->create();

        //$this->signIn();
      
        // $project= auth()->user()->projects()->create(
        //     factory(Project::class)->raw()
        // );

        // $task = $project->addTask('test task');

        $this->actingAs($project->owner)
        ->patch($project->tasks->first()->path(),[
            'body' => 'changed',
            'completed' => true
        ]);

        $this->assertDatabaseHas('tasks',[
            'body' => 'changed',
            'completed' => true
        ]);
    }   
    
    public function test_a_task_can_be_marked_as_incomplete(){
        $this->withoutExceptionHandling();
        
        $project = ProjectFactory::withTasks(1)->create();

        //$this->signIn();
      
        // $project= auth()->user()->projects()->create(
        //     factory(Project::class)->raw()
        // );

        // $task = $project->addTask('test task');

        $this->actingAs($project->owner)
        ->patch($project->tasks->first()->path(),[
            'body' => 'changed',
            'completed' => true
        ]);     
        
        $this->actingAs($project->owner)
        ->patch($project->tasks->first()->path(),[
            'body' => 'changed',
            'completed' => false
        ]);

        $this->assertDatabaseHas('tasks',[
            'body' => 'changed',
            'completed' => false
        ]);
    }

    public function test_a_task_requires_a_body(){
        // $this->signIn();
        // $project= auth()->user()->projects()->create(
        //     factory(Project::class)->raw()
        // );

        $project = ProjectFactory::create();
        $attributes = factory('App\Task')->raw(['body'=>'']);
        $this->actingAs($project->owner)
        ->post($project->path().'/tasks', [])->assertSessionHasErrors('body');
    }
}
