(function($, hb) { 
  $(document).ready(function (){
    hb.connectNow();
    hb.interval('fast');
  });

  $(document).on('heartbeat-send', function(e, data) {
    var date;
    date = new Date();
    date = date.getUTCFullYear() + '-' +
        ('00' + (date.getUTCMonth()+1)).slice(-2) + '-' +
        ('00' + date.getUTCDate()).slice(-2) + ' ' + 
        ('00' + date.getUTCHours()).slice(-2) + ':' + 
        ('00' + date.getUTCMinutes()).slice(-2) + ':' + 
        ('00' + date.getUTCSeconds()).slice(-2);
    data['date'] = date;
    console.log('data-sent:', data);
  });

  $(document).on('heartbeat-tick', function(e, data) {
    console.log('data-received', data);
    
    if($('#user-list-container').length) {
      /* empty both lists */
      $('#online_users').empty();
      $('#offline_users').empty();

      /* place online users */
      if(typeof data['online_users'] !== 'undefined') {
        data['online_users'].forEach(function(user) {
          if(user === data['user_login'])
            $('#online_users').append(`<li id="current-user">${user} (you)</li>`);
          else
            $('#online_users').append(`<li><a id="${user}" href="#" onclick="setCallee(event)">${user}</a></li>`);
        });
      }

      /* place offline users */
      if(typeof data['offline_users'] !== 'undefined') {
        data['offline_users'].forEach(function(user) {
          $('#offline_users').append(`<li>${user}</li>`);
        });
      }
    }
  });

}(jQuery, wp.heartbeat));
