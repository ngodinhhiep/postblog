<x-layouts pagetitle="{{$sharedData['user']->username}}'s profile">

    <div class="container py-md-5 container--narrow">
        <h2>
          @auth
          <img class="avatar-small" src="{{$sharedData['user']->avatar}}" /> {{$sharedData['user']->username}}
         
            @csrf
            @if (auth()->user()->id != $sharedData['user']->id)
              @if ($sharedData['followingExist'])
                <form class="ml-2 d-inline" action="/remove-follow/{{$sharedData['user']->username}}" method="POST">
                @csrf
                  <button class="btn btn-primary btn-sm" disabled>Following <i class="fas fa-user-check"></i>
                  </button>
                  <button class="btn btn-danger btn-sm">Unfollow <i class="fas fa-user-times"></i>
                  </button>
                </form>
              @else
              <form class="ml-2 d-inline" action="/create-follow/{{$sharedData['user']->username}}" method="POST">
                @csrf
                <button class="btn btn-primary btn-sm">Follow <i class="fas fa-user-plus"></i></button>
              </form>
              @endif
            @endif

            
            @if (auth()->user()->id == $sharedData['user']->id)
                <a href="/manage-avatar" class="btn btn-secondary btn-sm">Manage Avatar</a>
            @endif
          </form>
          @endauth
        </h2>
  
        <div class="profile-nav nav nav-tabs pt-2 mb-4">
          <a href="/profile/{{$sharedData['user']->username}}" class="profile-nav-link nav-item nav-link {{Request::segment(3) == "" ? "active" : ""}}">Posts: {{$sharedData['postCount']}}</a>
          <a href="/profile/{{$sharedData['user']->username}}/followers" class="profile-nav-link nav-item nav-link {{Request::segment(3) == "followers" ? "active" : ""}}">Followers: {{$sharedData['followerCount']}}</a>
          <a href="/profile/{{$sharedData['user']->username}}/following" class="profile-nav-link nav-item nav-link {{Request::segment(3) == "following" ? "active" : ""}}">Following: {{$sharedData['followingCount']}}</a>
        </div>


        <div class="profile-slot-content">
            {{$slot}}
        </div>
  

      </div>
</x-layouts>