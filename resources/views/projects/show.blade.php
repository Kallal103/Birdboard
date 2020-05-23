@extends('layouts.app')

 @section('content')
 <header class="flex items-center mb-3 pb-4">
    <div class=" flex justify-between items-end w-full">
      <p class=" text-gray-700 text-sm font-normal">
         <a href="/projects" class="text-gray-700 text-sm font-normal no-underline"> My Projects</a>  / {{$project->title}}

      </p>
                  <div class="flex items-center">
                @foreach ($project->members as $member)
                    <img
                        src="{{ gravatar_url($member->email) }}"
                        alt="{{ $member->name }}'s avatar"
                        class="rounded-full w-8 mr-2">
                @endforeach

                <img
                    src="{{ gravatar_url($project->owner->email) }}"
                    alt="{{ $project->owner->name }}'s avatar"
                    class="rounded-full w-8 mr-2">

                <a href="{{ $project->path().'/edit' }}" class="button ml-4">Edit Project</a>
            </div>
   
   </div>
 </header>

 <main>
     <div class=" lg:flex -mx-3">
         <div class=" lg:w-3/4 px-3 mb-6">
            <div class=" mb-8">
                <h2 class=" text-lg text-gray-700  font-normal mb-3">Tasks </h2>
                {{-- task --}}
                @foreach ($project->tasks as $task)
                <div class="card mb-3">
                <form action="{{$task->path()}}" method="POST">
                    @method('PATCH')
                    @csrf
                    <div class=" flex">
                      <input type="text" name="body" value="{{$task->body}}" class=" w-full  {{ $task->completed ? ' text-orange-500' : '' }}">
                      <input type="checkbox" name="completed" id="" onchange="this.form.submit()" 
                      {{ $task->completed ? 'checked' : '' }} >

                    </div>

                   
                  </form>
                 
              </div>
                @endforeach
                <div class="card mb-3">
                  <form action="{{$project->path().'/tasks'}}" method="POST">
                      @csrf
                      <input type="text" placeholder=" Add beginning new tasks ..." class=" w-full" name="body">
                    </form>
                  
                </div>
              
            </div>
           <div>
            <h2 class=" text-lg text-gray-700  font-normal mb-3 "> General Notes </h2>
         
            {{--general notes--}}
           <form method="POST" action="{{$project->path()}}">
            @csrf
            @method('PATCH')
            <textarea class="card w-full mb-4"
            name="notes"
             style=" min-height: 150px;" 
             placeholder=" Put down your notes here">
              {{$project->notes}}
          </textarea>
          <button type="submit" class=" button">
            Save
          </button>
          </form>
          @if ($errors->any())
          <div class="field mt-6">
             
                 @foreach ($errors->all() as $error)
   
                   <li class=" text-sm text-red-600">{{$error}}</li>
                     
                 @endforeach
                  
             
          </div>
          @endif

           </div>
           
         </div>
         <div class=" lg:w-1/4 px-3">
            
           @include('projects.card')
           @include('projects.activity.card')

         </div>

     </div>
 </main>

    
 @endsection