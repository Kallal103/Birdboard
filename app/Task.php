<?php

namespace App;
use App\Activity;
use App\Project;
use App\Task;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded = [];

    protected $touches = ['project'];

    protected $casts = [
        'completed' => 'boolean'
    ];

    

    public function complete()
    {
        $this->update(['completed'=> true]);

        $this->recordActivity('completed_task');
    }  
      public function incomplete()
    {
        $this->update(['completed'=> false]);

        $this->recordActivity('incompleted_task');
    }

    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function path()
    {
        return "/projects/{$this->project->id}/tasks/{$this->id}";
    }
    /* Record activity for a project.
    *
    * @param string $type
    */
    public function recordActivity($description)
    {
        $this->activity()->create([
            'project_id' => $this->project_id,
            'description' => $description
        ]);
    }

    /**
     * The activity feed for the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function activity()
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }
}
