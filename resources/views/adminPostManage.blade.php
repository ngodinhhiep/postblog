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
    <h2 style="text-align:center">Posts Management</h2>

    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>User</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($posts as $post)
            <tr>
                <td>{{ $post->id }}</td>
                <td>{{ $post->title }}</td>
                <td>{{ $post->user->username }}</td>
                <td>
                    <form action="/admin/posts/delete/{{$post->id}}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this post?')">Delete</button>
                    </form>
                    <button type="button" class="btn btn-primary btn-sm edit-post" data-post-id="{{ $post->id }}" data-toggle="modal" data-target="#editModal">Edit</button>
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
                        <label for="editTitle">Title</label>
                        <input type="text" class="form-control" name="post_title" id="editTitle">
                    </div>
                    <div class="form-group">
                        <label for="editBody">Body</label>
                        <textarea class="form-control" name="post_body" id="editBody"></textarea>
                    </div>
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

            $('.edit-post').on('click', function(event) {
                event.preventDefault();

                let postId = $(this).data('post-id');

                $.ajax({
                    url: "/admin/posts/details/" + postId,
                    method: 'GET',
                    success: function(response) {
                        $('#editModal #editBody').val(response.body);
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });

                let postTitle = $(this).closest('tr').find('td:nth-child(2)').text();

                // Set the values in the modal
                $('#editModal #editTitle').val(postTitle);

                // Set the post ID in the save button's data attribute
                $('#editModal .save-edit').data('post-id', postId);

                // Show the modal
                $('#editModal').modal('show');
            });

            $('.save-edit').on('click', function(event) {
                event.preventDefault();

                let postId = $(this).data('post-id');
                let newTitle = $('#editModal #editTitle').val();
                let newBody = $('#editModal #editBody').val();

                console.log(postId);
                console.log(newTitle);
                console.log(newBody);

                // Make the AJAX request
                $.ajax({
                    url: "/admin/posts_edit/" + postId,
                    method: 'POST',
                    data: {
                        post_title: newTitle,
                        post_body: newBody
                    },
                    success: function(response) {
                        // Handle success response
                        $('#editModal').modal('hide');
                        location.reload();
                    },
                    error: function(err) {
                        // Handle error response
                        console.log(err);
                    }
                });
            });
        });
    </script>
</x-layouts>
