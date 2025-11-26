@extends('layouts.mainlayout')

@section('extra-css')
<link rel="stylesheet" href="{{ asset('css/create.css') }}">

{{-- ✨ SUBTASK CSS --}}
<style>
    .subtask-box {
        border: 1px solid #ccc;
        padding: 15px;
        background: #f6f6f6;
        border-radius: 7px;
        margin-bottom: 12px;
    }
    .remove-subtask {
        background: red;
        color: #fff;
        border: none;
        padding: 5px 8px;
        border-radius: 4px;
        float: right;
        cursor: pointer;
    }
</style>
@endsection

@section('content')
<div class="dataform">
    <div class="form-title">
        <h1> Create new </h1>
    </div>

    <form action="{{ route('work.store') }}" method="post" enctype="multipart/form-data">
        @csrf

        <div class="form-item-container">
            <div class="form-section">

                <div class="form-items">
                    <label for="user_id">Assign task to :</label>
                    <select name="user_id" id="user_id" required>
                        <option value="">--Select user--</option>
                        @foreach ($users as $u )
                            <option value="{{ $u->id }}">{{ $u->username }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-items">
                    <label for="work_type">Work type :</label>
                    <select name="work_type" id="work_type" required>
                        <option value="task">Task</option>
                        <option value="testing">Testing</option>
                        <option value="error">Error</option>
                        <option value="learning">Learning</option>
                    </select>
                </div>

                <div class="form-items">
                    <label for="description">Description :</label>
                    <input type="text" name="description" id="description" required>
                </div>

                <div class="form-items" style="display:flex;flex-direction:column;">
                    <label for="days">Duration :</label>
                    <div class="duration">
                        <input type="number" name="days" id="days">
                        <select name="type_of_duration">
                            <option value="days">Days</option>
                            <option value="hours">Hours</option>
                        </select>
                    </div>
                </div>

            </div>

            <div class="form-section">

                <div class="form-items">
                    <label for="result">Status :</label>
                    <select name="result" id="result" required>
                        <option value="stuck">Stuck</option>
                        <option value="solved">Solved</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>

                <div class="form-items">
                    <label for="date">Date :</label>
                    <input type="date" name="date" id="date" required>
                </div>

                <div class="form-items">
                    <label for="attachments">Add attachment :</label>
                    <input type="file" name="attachments[]" multiple>
                </div>

            </div>
        </div>



        {{-- ✨ SUBTASK BLOCK --}}
        <hr>
        <h3>Subtasks (Optional)</h3>
        <button type="button" id="addSubtaskBtn">+ Add Subtask</button>

        <div id="subtaskContainer"></div>

        {{-- Hidden Template --}}
        <div id="subtaskTemplate" style="display:none;">
            <div class="subtask-box">
                <button type="button" class="remove-subtask" onclick="this.parentNode.remove()">Remove</button>

                <div class="form-items">
                    <label>Sub Work Type :</label>
                    <input type="text" class="sub_work_type" required>
                </div>

                <div class="form-items">
                    <label>Sub Work Result :</label>
                    <select class="sub_work_result" required>
                        <option value="stuck">Stuck</option>
                        <option value="solved">Solved</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>

                <div class="form-items">
                    <label>Sub Work Description :</label>
                    <input type="text" class="sub_work_description" required>
                </div>

                <div class="form-items">
                    <label>Work Status ID :</label>
                    <input type="number" class="work_status_id">
                </div>

            </div>
        </div>



        <button type="submit">Submit</button>
    </form>
</div>


{{-- ✨ SUBTASK SCRIPT --}}
<script>
    let subtaskIndex = 0;

    document.getElementById('addSubtaskBtn').addEventListener('click', function() {
        let template = document.querySelector('#subtaskTemplate .subtask-box');
        let clone = template.cloneNode(true);

        // Convert class → name="subtasks[index][field]"
        clone.querySelectorAll('input, select').forEach(input => {
            let field = input.classList[0];
            input.setAttribute('name', `subtasks[${subtaskIndex}][${field}]`);
        });

        document.getElementById('subtaskContainer').appendChild(clone);
        subtaskIndex++;
    });
</script>

@endsection
