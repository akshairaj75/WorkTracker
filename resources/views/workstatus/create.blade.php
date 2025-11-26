@extends('layouts.mainlayout')


@section('extra-css')
<link rel="stylesheet" href="{{ asset('css/create.css') }}">
@endsection


@section('content')
<div class="dataform">
      <div class="form-title">
            <h1> Create new </h1>
        </div>
    <form action="{{route('work.store')}}" method="post" enctype="multipart/form-data">
        @method('post')
        @csrf
        
      

        <div class="form-item-container">
            <div class="form-section">
                <div class="form-items">
                    <label for="user_id">Assign task to :
                    </label>
                    <select name="user_id" id="user_id" required>
                        <option value="">--Select user--</option>
                            @foreach ($users as $u )
                                <option value="{{ $u->id }}">{{ $u->username }} </option>                           
                            @endforeach
                    </select>
                </div>

                <div class="form-items">
                    <label for="work_type">Work type : </label>
                    <select name="work_type" id="work_type" required>
                        <option value="task">Task</option>
                        <option value="testing">Testing</option>
                        <option value="testing">Testing</option>
                        <option value="error">Error</option>
                        <option value="learning">Learning</option>
                    </select>
                </div>

                <div class="form-items">
                    <label for="description">Description : </label>
                    <input type="text" name="description" id="description" required>
                </div>

                <div class="form-items" style="display: flex; flex-direction: column;">
                    <label for="days">Duration :</label>                        
                    <div class="duration">
                        <input type="number" name="days" id="days">

                        <select name="type_of_duration" id="">
                            <option value="days">Days</option>
                            <option value="hours">Hours</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-items">
                    <label for="result">Status : </label>
                    <select name="result" id="result" required>
                        <option value="stuck">Stuck</option>
                        <option value="solved">Solved</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>

                <div class="form-items">
                    <label for="date">Date : </label>
                    <input type="date" name="date" id="date" required>
                </div>    
                <div class="form-items">
                    <label for="attachments">Add attatchment : </label> 
                    <input type="file" name="attachments[]" multiple id="attachments">
                </div>    
            </div>
        </div>  

     <!-- Hidden template -->

        <div id="subtaskTemplate" style="display:none;">
            <div class="subtask-box">
                <button type="button" class="remove-subtask">Remove</button>

                <div class="form-items">
                    <label>Sub Work Type:</label>
                    <select class="sub_work_type">
                        <option value="">--Select--</option>
                        <option value="task">Task</option>
                        <option value="testing">Testing</option>
                        <option value="error">Error</option>
                        <option value="learning">Learning</option>
                    </select>
                </div>

                <div class="form-items">
                    <label>Sub Work Result:</label>
                    <select class="sub_work_result">
                        <option value="">--Select--</option>
                        <option value="stuck">Stuck</option>
                        <option value="solved">Solved</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>

                <div class="form-items">
                    <label>Sub Work Description:</label>
                    <input type="text" class="sub_work_description">
                </div> 
                <div class="form-items">
                    <label>Date:</label>
                    <input type="date" class="sub_work_date">
                </div>

                <!-- Nested subtasks container -->
                <div class="nested-subtask-container"></div>
                <button type="button" class="addNestedSubtaskBtn">+ Add Nested Subtask</button>
            </div>
        </div>

        <!-- Container for subtasks -->
        <div id="subtaskContainer"></div>
        <button type="button" id="addSubtaskBtn">+ Add Subtask</button>


        <button type="submit">Submit</button>        
    </form>   
</div>

<script>
    let subtaskIndex = 0;

    function createNestedRow(parentIndex, nestedIndex) {
        const wrapper = document.createElement('div');
        wrapper.classList.add('nested-box');
        wrapper.innerHTML = `
            <div style="border:1px solid #ddd; padding:8px; margin:8px 0;">
                <button type="button" class="remove-nested" style="float:right">Remove</button>
                <div class="form-items">
                    <label>Sub Work Type:</label>
                    <select name="subtasks[${parentIndex}][nested][${nestedIndex}][sub_work_type]" required>
                        <option value="">--Select--</option>
                        <option value="task">Task</option>
                        <option value="testing">Testing</option>
                        <option value="error">Error</option>
                        <option value="learning">Learning</option>
                    </select>
                </div>
                <div class="form-items">
                    <label>Sub Work Result:</label>
                    <select name="subtasks[${parentIndex}][nested][${nestedIndex}][sub_work_result]" required>
                        <option value="">--Select--</option>
                        <option value="stuck">Stuck</option>
                        <option value="solved">Solved</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div class="form-items">
                    <label>Sub Work Description:</label>
                    <input type="text" name="subtasks[${parentIndex}][nested][${nestedIndex}][sub_work_description]" required>
                </div>
                <div class="form-items">
                    <label>Date:</label>
                    <input type="date" name="subtasks[${parentIndex}][nested][${nestedIndex}][sub_work_date]" required>
                </div>
            </div>
        `;
        // remove handler
        wrapper.querySelector('.remove-nested').addEventListener('click', () => wrapper.remove());
        return wrapper;
    }

    document.getElementById('addSubtaskBtn').addEventListener('click', function() {
        const template = document.querySelector('#subtaskTemplate .subtask-box');
        const clone = template.cloneNode(true);
        const currentIndex = subtaskIndex;
        clone.style.display = 'block';
        clone.dataset.index = currentIndex;

        // set names for top-level subtask fields
        clone.querySelectorAll('input, select').forEach(field => {
            // skip buttons
            if (field.matches('button')) return;
            // class names contain the field key (e.g., sub_work_type)
            const classList = Array.from(field.classList);
            const fieldClass = classList.find(c => c.startsWith('sub_'));
            if (fieldClass) {
                field.setAttribute('name', `subtasks[${currentIndex}][${fieldClass}]`);
                field.required = true;
            }
        });

        // remove top-level subtask
        const removeBtn = clone.querySelector('.remove-subtask');
        removeBtn.addEventListener('click', () => clone.remove());

        // nested handling per subtask
        const nestedContainer = clone.querySelector('.nested-subtask-container');
        let nestedIndex = 0;
        const addNestedBtn = clone.querySelector('.addNestedSubtaskBtn');
        addNestedBtn.addEventListener('click', () => {
            const nestedRow = createNestedRow(currentIndex, nestedIndex);
            nestedContainer.appendChild(nestedRow);
            nestedIndex++;
        });

        // append to page
        document.getElementById('subtaskContainer').appendChild(clone);
        subtaskIndex++;
    });
</script>

   
@endsection