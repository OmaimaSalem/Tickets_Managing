
@extends('frontend.layouts.app')
@php
  $date = date("Y-m-d");
  $days = array( 'Monday', 'Tuesday', 'Wednesday','Thursday','Friday', 'Saturday','Sunday');
  @endphp

  @push('extra-css')
    <link href='{{ asset("css/fullcalendar.min.css") }}' rel='stylesheet' />
  @endpush


  @section('extra-style')
  <style>
    body {
        margin: 40px 10px;
        padding: 0;
        font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
        font-size: 14px;
      }
      #calendar {
        max-width: 1100px;
        margin: 0 auto;
      }
      .ui-timepicker-container {
        z-index: 99999!important;
    }
    </style>
  @endsection

@section('content')


<h4>{{ $user->name }} Calendar</h4>
    <div id='calendar-container'>
      <div id='calendar'></div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="addGuestEventModel" tabindex="-1" role="dialog" aria-labelledby="addGuestEventModelLabrl" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Add event</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form>
            
            <div class="form-group">
              <label for="name-input" class="col-form-label">Name:</label>
                    <input type="text" class="form-control" id="name-input">
                  </div>
                  
                  <div class="form-group">
                    <label for="email-input" class="col-form-label">Email:</label>
                    <input type="text" class="form-control" id="email-input">
                </div>
                
                <div class="form-group">
                    <label for="mobile-input" class="col-form-label">Mobile:</label>
                    <input type="text" class="form-control" id="mobile-input">
                  </div>

                  <div class="form-group">
                  <label for="date-input" class="col-form-label">Date:</label><div class="date"></div><br />
                  <input type="hidden" id="date-input">
                  
                  <label for="date-input" class="col-form-label">From:</label><div class="from">
                    
                  <input type="text" class="form-control  frompicker" readonly id="from-input"></div><br />
                  
                  <label for="date-input" class="col-form-label">To:  </label><div class="to">
                    <input type="text" class="form-control  topicker" readonly id="to-input">
                  </div>
                  
                  <label for="date-input" class="col-form-label">Title:  </label><div class="to">
                    <input type="text" class="form-control  title" id="title">
                  </div>
                  
                  <label for="date-input" class="col-form-label">description:  </label><div class="to">
                    <textarea type="text" class="form-control description" id="description"></textarea>
                  </div>

                  <label for="date-input" class="col-form-label">Attachment:  </label><div class="to">
                    <input type="file" class="form-control" id="attachment" />
                  </div>

                </div>

              </form>
              
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary add_event">Save changes</button>
        </div>
      </div>
    </div>
  </div>
  
  
  
  @endsection
  @push('extra-js')
    <script src='{{ asset("js/fullcalendar.min.js") }}'></script>
  @endpush

  @section('extra-scripts')
  <script>
    var bookingTimes = {!! $bookingTimes !!};
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
    
        var calendar = new FullCalendar.Calendar(calendarEl, {
          timeZone: 'EET',
          headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
          },
        //   initialDate: '2020-09-12',
          eventOverlap: false,
          navLinks: true, // can click day/week names to navigate views
          selectable: true,
          selectMirror: true,
          firstDay: 1,
          select: function(arg) {
            // var title = prompt('Event Title:');
            // if (title) {
            //   calendar.addEvent({
            //     title: title,
            //     start: arg.start,
            //     end: arg.end,
            //     allDay: arg.allDay
            //   })
            // }
            // calendar.unselect()
            // $day = getDate(arg.start);
            // $("#date-input").val(arg.start);
            // $("#addGuestEventModel .date").text($day);
            // $("#addGuestEventModel").modal("show");
          },
          validRange: {
            start: new Date().toISOString().slice(0,10)
          },
          eventClick: function(arg) {
            $day = getDate(arg.event.start);
            $("#date-input").val(arg.event.start);
            $(".frompicker").val(getTime(arg.event.start));
            $(".topicker").val(getTime(arg.event.end));
            $("#addGuestEventModel .date").text($day);
            $("#addGuestEventModel").modal("show");
          },
          hiddenDays: [ 0,6 ], 
          editable: true,
          dayMaxEvents: true, // allow "more" link when too many events
          events: [
            @foreach($available_times as $event)
            {
              title: "from {{$event->from}} to {{$event->to}}",
              date: "{{ date('Y-m-d', strtotime($days[$event->day_number - 1], strtotime($date))) }}",
              start: "{{ date('Y-m-d', strtotime($days[$event->day_number - 1], strtotime($date))) }}T{{ $event->from}}",
              end:  "{{ date('Y-m-d', strtotime($days[$event->day_number - 1], strtotime($date))) }}T{{ $event->to}}",              
              startTime: "{{ $event->from}}",
              endTime: "{{ $event->to}}",
              daysOfWeek: [{{ $event->day_number == 7 ? 0: $event->day_number }}],
              excludedDate: new Date('2021/02/15'),
              allDay:false
            },
            @endforeach
          ],
    
        //   eventDidMount: function(event){              
        //     var theDate = event.start
        //       var excludedDate = event.excludedDate;
        //       console.log(excludedDate);
        //       var excludedTomorrrow = new Date(excludedDate);
        //       //if the date is in between August 29th at 00:00 and August 30th at 00:00 DO NOT RENDER
        //       if( theDate >= excludedDate && theDate < excludedTomorrrow.setDate(excludedTomorrrow.getDate() + 1) ) {
        //           return false;
        //       }

        // },

        eventContent: function(info) {
          // console.log(getDate(info.event.start)+"T"+getTime(info.event.start)+":00",bookingTimes);
          if(bookingTimes.includes(getDate(info.event.start)+"T"+getTime(info.event.start)+":00")) {
            info.event.display = 'none';
            return false;
          }
          // var theDate = info.event.start
          // var excludedDate = info.event.extendedProps.excludedDate;
          // var excludedTomorrrow = new Date(excludedDate);
          //     if( theDate >= excludedDate && theDate < excludedTomorrrow.setDate(excludedTomorrrow.getDate() + 1) ) {
          //       return false;
          //     }
          //   },
        },
          });
        $('.add_event').click(function(){
    
              let event = {
                title: $("#title").val(),
                date: getDateToCalendar($("#date-input").val()),
                start: $(".frompicker").val(),
                end: $(".topicker").val(),
                description: $(".description").val(),
                name:   $("#name-input").val(),
                email:  $("#email-input").val(),
                mobile: $("#mobile-input").val(),
              };
    
              if(event.title.trim() == "" || 
                 event.date.trim() == "" || 
                 event.start.trim() == "" || 
                 event.end.trim() == "" || 
                 event.description.trim() == "" || 
                 event.name.trim() == "" || 
                 event.email.trim() == "" || 
                 event.mobile.trim() == ""
                 ){
                   alert("All fields are required");
                   return false;
                 }
                 var attachment = $('#attachment')[0].files;

                 fd = new FormData();

                 fd.append('attachment',attachment[0]);
                 fd.append('user_id',{{ $user->id}});

                 Object.keys(event).forEach(element => {
                   fd.append(element,event[element]);
                 });


              $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                      'X-Requested-With' : 'XMLHttpRequest'
                  }
              });
    
    
              $.ajax({
                method: 'POST',
                crossDomain: true,
                dataType: 'json',
                crossOrigin: true,
                processData: false,
                contentType: false,
                data: fd,
                url: '{{ route("ajax_calendar_add_event") }}',
                success: function(response){
                  // calendar.addEvent({
                  //       title: response.title,
                  //       date:  response.date,
                  //       start: response.date+"T"+response.from,
                  //       end:   response.date+"T"+response.to,
                  //       description: response.description,
                  // });
                  $("#addGuestEventModel").modal("hide");
    
                },
                error: function (request, status, error) {
                    console.log("There was an error: ", request.responseText);
                }
              });
    
              calendar.unselect();
            });
    
    
        calendar.render();
    
    
    
    
    
      });
    
    
            function getDate(date) {
                let newdate = new Date(date);
                let year = newdate.getFullYear();
                let month = `${newdate.getMonth() + 1}`.padStart(2, "0");
                let day = `${newdate.getDate()}`.padStart(2, "0");
                let stringDate = [day, month, year].join("-");
    
                // let hours = `${newdate.getHours()}`.padStart(2, "0");
                // let minutes = `${newdate.getMinutes()}`.padStart(2, "0");
                // let seconds = `${newdate.getSeconds()}`.padStart(2, "0");
                // let stringTime = [hours, minutes, seconds].join(":");
    
                let fullDate = stringDate /*+ " " + stringTime*/;
                return fullDate;
            }
            function getDateToCalendar(date){
              let newdate = new Date(date);
                let year = newdate.getFullYear();
                let month = `${newdate.getMonth() + 1}`.padStart(2, "0");
                let day = `${newdate.getDate()}`.padStart(2, "0");
                let stringDate = [year, month, day].join("-");
                let fullDate = stringDate;
                return fullDate;
            }
            function getTime(date) {
                let newdate = new Date(date);
                let hours = `${newdate.getHours()}`.padStart(2, "0");
                let minutes = `${newdate.getMinutes()}`.padStart(2, "0");
                //let seconds = `${newdate.getSeconds()}`.padStart(2, "0");
                //let stringTime = [hours, minutes, seconds].join(":");
                let stringTime = [hours, minutes].join(":");
    
                return stringTime;
            }
    
          
    
    </script>
    @endsection

