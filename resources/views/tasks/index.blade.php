<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP - Simple To Do List App</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .btn-check { background-color: #28a745; border-color: #28a745; }
        .btn-delete { background-color: #dc3545; border-color: #dc3545; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">PHP - Simple To Do List App</h1>
        <div class="input-group mb-3">
            <input type="text" id="task-name" class="form-control" placeholder="Enter task">
            
        </div>
        <div class="row mb-3">
            <div class="col">
                    <button id="add-task" class="btn btn-primary">Add Task</button>
                    <button id="show-all-tasks" class="btn btn-secondary">Show All Tasks</button>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="tasks-list"></tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
       
            $(document).ready(function() {
                function fetchTasks(showAll) {
                    $.ajax({
                        url: '/getTasks',
                        method: 'GET',
                        success: function(response) {
                            $('#tasks-list').empty();
                                if (Array.isArray(response)) { 
                                    response.forEach((task, index) => {
                                        if(showAll === true){
                                            let statusBadge = task.status === 1 ? `<span class="badge badge-success">Completed</span>` : `<span class="badge badge-secondary">Pending</span>`;
                                            let actions = `<button class="btn btn-sm btn-check ${task.status === 1 ? 'completed' : ''}" data-id="${task.id}"><i class="fas fa-check"></i></button> 
                                                        <button class="btn btn-sm btn-delete ml-2" data-id="${task.id}"><i class="fas fa-trash"></i></button>`;
                                            $('#tasks-list').append(
                                                `<tr data-task-id="${task.id}" class="${task.status === 1 ? 'completed-task' : ''}">
                                                    <td>${index + 1}</td>
                                                    <td>${task.name}</td>
                                                    <td>${statusBadge}</td>
                                                    <td>${actions}</td>
                                                </tr>`
                                            );
                                        }else{
                                            if(task.status===0){
                                            let statusBadge = task.status === 1 ? `<span class="badge badge-success">Completed</span>` : `<span class="badge badge-secondary">Pending</span>`;
                                            let actions = `<button class="btn btn-sm btn-check ${task.status === 1 ? 'completed' : ''}" data-id="${task.id}"><i class="fas fa-check"></i></button> 
                                                        <button class="btn btn-sm btn-delete ml-2" data-id="${task.id}"><i class="fas fa-trash"></i></button>`;
                                            $('#tasks-list').append(
                                                `<tr data-task-id="${task.id}" class="${task.status === 1 ? 'completed-task' : ''}">
                                                    <td>${index + 1}</td>
                                                    <td>${task.name}</td>
                                                    <td>${statusBadge}</td>
                                                    <td>${actions}</td>
                                                </tr>`
                                            );
                                        }
                                        }    
                                    });
                                } else {
                                    console.error('Unexpected response format:', response);
                                }
                        },
                        error: function(error) {
                            console.error('Error fetching tasks', error);
                        }
                    });
            }

            fetchTasks(false);

            $('#add-task').click(function() {
                const taskName = $('#task-name').val();
                if (taskName.trim() !== '') {
                    $.ajax({
                        url: '/tasks',
                        method: 'POST',
                        data: {
                            name: taskName,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {

                            fetchTasks(); 
                            $('#task-name').val(''); 
                        },
                        error: function(error) {
                            console.error('Error adding task', error);
                            if (error) {
                                alert(error.responseJSON.message); 
                            } else {
                                alert('Error adding task. Please try again.');
                            }
                        }
                    });
                }
                $('#add-task').css({
                    'background-color': '#007bff', // Blue background color
                    'color': '#fff', // White text color
                    'border-color': '#007bff' // Blue border color (optional)
                });

                $('#show-all-tasks').css({
                    'background-color': '#6c757d', // Grey background color
                    'color': '#fff', // White text color
                    'border-color': '#6c757d' // Grey border color
                });

            });

            $(document).on('click', '.btn-check', function() {
                const taskId = $(this).data('id');
                $.ajax({
                    url: `/tasks/${taskId}`,
                    method: 'PUT',
                    data: {
                        completed: true, 
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        fetchTasks();
                    },
                    error: function(error) {
                        console.error('Error marking task as completed', error);
                    }
                });
            });

            $(document).on('click', '.btn-delete', function() {
                if (confirm('Are you sure you want to delete this task?')) {
                    const taskId = $(this).data('id');
                    $.ajax({
                        url: `/tasks/${taskId}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $(`tr[data-task-id="${taskId}"]`).remove();
                        },
                        error: function(error) {
                            console.error('Error deleting task', error);
                        }
                    });
                }
            });

            $('#show-all-tasks').click(function() {
                fetchTasks(true); 
                $('#show-all-tasks').css({
                    'background-color': '#007bff', 
                    'color': '#fff',
                    'border-color': '#007bff' 
                });
                $('#add-task').css({
                    'background-color': '#6c757d',
                    'color': '#fff',
                    'border-color': '#6c757d' 
                });
            });
            
            $('#task-name').keypress(function(event) {
                if (event.keyCode === 13) {
                    $('#add-task').click(); 
                }
            });
        });

    </script>
</body>
</html>
