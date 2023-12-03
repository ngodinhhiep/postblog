<x-layouts>
    <div class="d-flex">
        <div class="sidebar collapsed">
            <ul class="nav flex-column mt-5 text-center">
                <li class="nav-item">
                    <a class="nav-link" href="/admin/users">Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/admin/posts">Posts</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Contact</a>
                </li>
            </ul>
        </div>
    </div>

    <button class="btn btn-primary toggle-sidebar"> <i class="fas fa-bars"></i></button>
    
    <div class="content">
    <h2 style="text-align: center" class="mb-5 mt-3">Users Management</h2>
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Posts Count</th>
                <th>Followers Count</th>
                <th>Following Count</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->username }}</td>
                <td>{{ $user->posts->count() }}</td>
                <td>{{ $user->followers->count() }}</td>
                <td>{{ $user->followedUser->count() }}</td>
                <td>
                    <div class="btn-group">
                        <form action="/admin/users/delete/{{$user->id}}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                        </form>
                        <button type="button" class="btn btn-primary edit-user" data-user-id="{{ $user->id }}" data-toggle="modal" data-target="#editModal_{{ $user->id }}">Edit</button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
    
    

     <!-- Edit Modal -->
     <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editForm">
                    @csrf
                    <div class="form-group">
                        <label for="editUsername">Username</label>
                        <input type="text" class="form-control" name="user_name" id="editUsername">
                    </div>
                    {{-- <div class="form-group">
                        <label for="editBody">Body</label>
                        <input class="form-control" name="post_body" id="editBody">
                    </div> --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary save-edit">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div> 

    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
       

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.toggle-sidebar').click(function() {
                $('.sidebar').toggleClass('collapsed');
                $('.content').toggleClass('collapsed');
            });

            $('.edit-user').on('click', function(event) {
                event.preventDefault();
    
                let userId = $(this).data('user-id');
                let username = $(this).closest('tr').find('td:nth-child(2)').text();
               
                $('#editModal #editUsername').val(username);
               
                $('#editModal .save-edit').data('user-id', userId); 
                $('#editModal').modal('show');
            });
    
            $('.save-edit').on('click', function(event) {
                event.preventDefault();
    
                let userId = $(this).data('user-id'); 
                let newUsername = $('#editModal #editUsername').val();
    
                console.log(userId);
                console.log(newUsername);
    
                $.ajax({
                    url: "/admin/users_edit/"+ userId,
                    method: 'POST',
                    data: {
                        username: newUsername,

                    },
                    success: function(response) {
                        $('#addModal').modal('hide');
                        location.reload();
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
            });
        });
    </script>
</x-layouts>