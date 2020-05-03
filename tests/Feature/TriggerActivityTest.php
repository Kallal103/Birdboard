<?php

namespace Tests\Feature;

use App\Project;
use App\Task;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TriggerActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_project()
    {
        $project = ProjectFactory::create();

        $this->assertCount(1, $project->activity);

       
        tap($project->activity->last(), function ($activity) {
            $this->assertEquals('created_project', $activity->description);

            $this->assertNull($activity->changes);
        });
    }

    public function test_updating_a_project()
    {
        $project = ProjectFactory::create();
        $originalTitle = $project->title;

        $project->update(['title' => 'Changed']);

        $this->assertCount(2, $project->activity);

        tap($project->activity->last(), function($activity) use ($originalTitle) {
            $this->assertEquals('updated_project', $activity->description);
            $expected = [
                'before' => ['title' => $originalTitle],
                'after' => ['title' => 'Changed']
            ];
            $this->assertEquals($expected, $activity->changes);
        });
        
    }

    public function test_creating_a_new_task()
    {
        $project = ProjectFactory::create();

        $project->addTask('Some Task');

        $this->assertCount(2, $project->activity);
        //$this->assertEquals('created_task', $project->activity->last()->description);

        tap($project->activity->last(), function($activity){
            $this->assertEquals('created_task', $activity->description);
            $this->assertInstanceOf(Task::class, $activity->subject);
            $this->assertEquals('Some Task', $activity->subject->body);
        });

       

    }

    public function test_completing_a_task()
    {
        $project = ProjectFactory::withTasks(1)->create();

        $this->actingAs($project->owner)
        ->patch($project->tasks->first()->path(),[
            'body' => 'changed',
            'completed' => true
        ]);

         $this->assertCount(3, $project->activity);

         $this->assertEquals('completed_task', $project->activity->last()->description);

         tap($project->activity->last(), function ($activity) {
            $this->assertEquals('completed_task', $activity->description);
            $this->assertInstanceOf(Task::class, $activity->subject);
        });

    }
    
    public function test_incompleting_a_task()
    {
        $project = ProjectFactory::withTasks(1)->create();

        $this->actingAs($project->owner)
        ->patch($project->tasks->first()->path(),[
            'body' => 'changed',
            'completed' => true
        ]);

         $this->assertCount(3, $project->activity);

         $this->patch($project->tasks->first()->path(),[
            'body' => 'changed',
            'completed' => false
        ]);
        
        $project->refresh();
        $this->assertCount(4, $project->activity);

        $this->assertEquals('incompleted_task', $project->activity->last()->description);

    }

    public function test_deleting_a_task(){
        $project = ProjectFactory::withTasks(1)->create();
        $project->tasks[0]->delete();
        $this->assertCount(3, $project->activity);

    }
}
