$(document).ready(function () {
    var consumedTimers = {};
    var remainingTimers = {};
    var intervals = {};

    // Function to save the task timer via AJAX
    function save_task_timer(taskId, consumed_hours, reamining_hours) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "/saveHours",
            method: "POST",
            data: {
                task_id: taskId,
                consumed_hours: consumed_hours,
                reamining_hours: reamining_hours
            },
            success: function (response) {
                if (response.success) {
                    console.log(response.message);
                } else {
                    alert("Failed to save task timer!");
                }
            },
        });
    }

    // Function to format time from seconds to hh:mm:ss
    function formatTime(seconds) {
        let hrs = Math.floor(seconds / 3600);
        let mins = Math.floor((seconds % 3600) / 60);
        let secs = seconds % 60;
        return `${String(hrs).padStart(2, '0')}:${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
    }

    // Function to convert time (hh:mm:ss) into total seconds
    function timeToSeconds(time) {
        var parts = time.split(':');
        return (+parts[0] * 3600) + (+parts[1] * 60) + (+parts[2]);
    }


    // Load checkbox and timer states from localStorage on page load
    $('.task-checkbox').each(function () {
        var taskId = $(this).data('task-id');
        var assigned_hours = $('#assigned_hours_' + taskId).text();
        var lasttimerVlue = 3600 * assigned_hours;
        var isChecked = localStorage.getItem('checkbox_' + taskId);
        var savedConsumed = localStorage.getItem('consumed_time_' + taskId);
        var savedRemaining = localStorage.getItem('remaining_time_' + taskId);
        var isDisabled = localStorage.getItem('checkbox_disabled_' + taskId); // Check if the checkbox is disabled


        // If the task is disabled, permanently disable the checkbox
        if (isDisabled === 'true') {
            $(this).prop('disabled', true);
            return;
        }

        if (isChecked === 'true') {
            $(this).prop('checked', true);
            $('.task-checkbox').not(this).prop('disabled', true);

            // Retrieve saved timer values or default to 0 consumed and 4 hours remaining
            consumedTimers[taskId] = savedConsumed ? parseInt(savedConsumed) : 0;
            remainingTimers[taskId] = savedRemaining ? parseInt(savedRemaining) : lasttimerVlue;

            // Update UI with saved times
            $('#consumed_hours_' + taskId).text(formatTime(consumedTimers[taskId]));
            $('#remaining_hours_' + taskId).text(formatTime(remainingTimers[taskId]));

            // Resume the interval
            intervals[taskId] = setInterval(function () {
                consumedTimers[taskId] += 1;
                remainingTimers[taskId] -= 1;

                $('#consumed_hours_' + taskId).text(formatTime(consumedTimers[taskId]));
                $('#remaining_hours_' + taskId).text(formatTime(remainingTimers[taskId]));

                // Save the updated timer values to localStorage
                localStorage.setItem('consumed_time_' + taskId, consumedTimers[taskId]);
                localStorage.setItem('remaining_time_' + taskId, remainingTimers[taskId]);

                // Stop the timer when limits are reached
                if (consumedTimers[taskId] >= lasttimerVlue || remainingTimers[taskId] <= 0) {
                    clearInterval(intervals[taskId]);
                    alert("Timer completed for task " + taskId);

                    // Disable the checkbox and save its state
                    $('.task-checkbox[data-task-id="' + taskId + '"]').prop('disabled', true);
                    localStorage.setItem('checkbox_disabled_' + taskId, 'true');

                    // Save the disabled state via AJAX
                    save_task_timer(taskId, formatTime(consumedTimers[taskId]), formatTime(remainingTimers[taskId]), true);
                }
            }, 1000); // Update every 1 second
        }
    });

    // Handle checkbox state changes
    $('.task-checkbox').on('change', function () {
        var taskId = $(this).data('task-id');
        var consumed_hours = $('#consumed_hours_' + taskId).text();
        var reamining_hours = $('#remaining_hours_' + taskId).text();
        var assigned_hours = $('#assigned_hours_' + taskId).text();
        var lasttimerVlue = 3600 * assigned_hours;

        if ($(this).is(':checked')) {
            // Save state to localStorage
            localStorage.setItem('checkbox_' + taskId, 'true');

            if (!consumedTimers[taskId]) {
                consumedTimers[taskId] = timeToSeconds(consumed_hours); // Start from saved or 0
            }

            if (!remainingTimers[taskId]) {
                remainingTimers[taskId] = timeToSeconds(reamining_hours); // Start from saved or 4 hours
            }

            // Start the interval
            intervals[taskId] = setInterval(function () {
                consumedTimers[taskId] += 1;
                remainingTimers[taskId] -= 1;

                $('#consumed_hours_' + taskId).text(formatTime(consumedTimers[taskId]));
                $('#remaining_hours_' + taskId).text(formatTime(remainingTimers[taskId]));

                // Save the updated timer values to localStorage
                localStorage.setItem('consumed_time_' + taskId, consumedTimers[taskId]);
                localStorage.setItem('remaining_time_' + taskId, remainingTimers[taskId]);

                // Stop the timer when limits are reached
                if (consumedTimers[taskId] >= lasttimerVlue || remainingTimers[taskId] <= 0) {
                    clearInterval(intervals[taskId]);
                    alert("Timer completed for task " + taskId);

                    // Disable the checkbox and save its state
                    $('.task-checkbox[data-task-id="' + taskId + '"]').prop('disabled', true);
                    localStorage.setItem('checkbox_disabled_' + taskId, 'true');

                    // Save the disabled state via AJAX
                    save_task_timer(taskId, formatTime(consumedTimers[taskId]), formatTime(remainingTimers[taskId]), true);
                }
            }, 1000);

            // Disable all other checkboxes
            $('.task-checkbox').not(this).prop('disabled', true);
        } else {
            // Remove state from localStorage
            localStorage.removeItem('checkbox_' + taskId);
            localStorage.removeItem('consumed_time_' + taskId);
            localStorage.removeItem('remaining_time_' + taskId);

            clearInterval(intervals[taskId]);

            // Re-enable all checkboxes if not disabled
            $('.task-checkbox').each(function () {
                var isDisabled = localStorage.getItem('checkbox_disabled_' + $(this).data('task-id'));
                if (isDisabled !== 'true') {
                    $(this).prop('disabled', false);
                }
            });

            // Save timer values via AJAX
            save_task_timer(taskId, consumed_hours, reamining_hours);
        }
    });

    setTimeout(function () {
        $(".alert").addClass("d-none");
    }, 3000);

    $('.btn-view-task').on('click', function (e) {
        e.preventDefault();

        var taskId = $(this).data('task-id'); // Get the task ID
        var taskDetails = $('#task_detail_' + taskId).html(); // Fetch the hidden task details

        // Load the task details into the modal
        $('#modalContent').html(taskDetails);

        // Show the modal
        $('#taskModal').modal('show');
    });

    //#################### Break Management Start ####################

    function ajax_break_start() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '/break/start',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                console.log(response.message);
            },
            error: function (xhr) {
                console.log(xhr.responseJSON.message);
            }
        });
    }

    function ajax_break_end() {
        $.ajax({
            url: '/break/end/',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                console.log(response.message);
            },
            error: function (xhr) {
                console.log(xhr.responseJSON.message);
            }
        });
    }

    var breakStatus = localStorage.getItem('breakCheckboxState');
    if (breakStatus === 'checked') {
        $('.break-checkbox').prop('checked', true);
        $('.breakstatus').html("Status : On Break");
    } else {
        $('.break-checkbox').prop('checked', false);
        $('.breakstatus').html("Status : Working");
    }

    $('.break-checkbox').on('change', function () {
        if ($(this).is(':checked')) {
            $('.breakstatus').html("Status : On Break");
            localStorage.setItem('breakCheckboxState', 'checked');
            ajax_break_start()
        } else {
            $('.breakstatus').html("Status : Working");
            localStorage.setItem('breakCheckboxState', 'unchecked');
            ajax_break_end()
        }
    });


    function checkCheckboxes() {
        var isTaskChecked = $('.task-checkbox').is(':checked');
        var isBreakChecked = $('#break-checkbox').is(':checked');

        // Check if both checkboxes are unchecked
        if (!isTaskChecked && !isBreakChecked) {
            alert("You Are Setting Idele From 5 Minuets!");
        }
    }

    // Set a 10-second delay to check the checkboxes
    setTimeout(checkCheckboxes, 300000);


    // #################### Break Management End ####################

    $(function () {
        const dateFormat = "mm/dd/yy";

        function initializeDatepickers(fromSelector, toSelector, onChangeCallback) {
            var from = $(fromSelector).datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                changeYear: true,
                numberOfMonths: 1
            }).on("change", function () {
                to.datepicker("option", "minDate", getDate(this));
                if (onChangeCallback) onChangeCallback();
            });

            var to = $(toSelector).datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                changeYear: true,
                numberOfMonths: 1
            }).on("change", function () {
                from.datepicker("option", "maxDate", getDate(this));
                if (onChangeCallback) onChangeCallback();
            });

            function getDate(element) {
                try {
                    return $.datepicker.parseDate(dateFormat, element.value);
                } catch (error) {
                    return null;
                }
            }
        }

        function triggerAjax(url, data, targetElement) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: url,
                method: "GET",
                data: data,
                success: function (response) {
                    console.log(response);
                    $(targetElement).html(response.html);
                }
            });
        }

        //############# Project Filter Datepicker ############# 
        initializeDatepickers("#from", "#to", function () {
            var fromDate = $("#from").val();
            var toDate = $("#to").val();
            if (fromDate && toDate) {
                triggerAjax('/filter-projects', { from: fromDate, to: toDate }, '#allProjects');
            }
        });

        //#############  Task Filter Datepicker ############# 
        initializeDatepickers("#task_from", "#task_to");

        // Task Filter AJAX Call
        $(document).on('click', '#task_filter', function () {
            var fromDate = $("#task_from").val();
            var toDate = $("#task_to").val();
            var user_id = $('#user_data').val();

            if ((fromDate && toDate) || user_id) {
                triggerAjax('/filter-tasks', { from: fromDate, to: toDate, user_id: user_id }, '#allTasks');
            }
        });
    });

});

