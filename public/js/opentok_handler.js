var session = null;
var publisher = null;

function handleError(error) {
  console.log(error);
}

function setMode(which) {
  switch(which) {
    case 'call':
      jQuery('#end-call-btn').show();
      jQuery('#videos').show();
      jQuery('#user-list-container').hide();
      jQuery('#user-list-container').hide();
      jQuery('#contact-to-call').hide();
      break;
    case 'contacts':
      jQuery('#end-call-btn').hide();
      jQuery('#videos').hide();
      jQuery('#user-list-container').show();
      jQuery('#contact-to-call').hide();
      break;
    case 'call-option':
      jQuery('#contact-to-call').show();
  }
}

function initPublisher(which) {
  var options = {
    insertMode: 'append',
    width: '100%',
    height: '100%',
  }
  if(which === 'share-screen-btn' && php.extension_id !== '') {
    OT.registerScreenSharingExtension(
      "chrome",
      php.extension_id,
      2
    );
    options['videoSource'] = 'screen';
  }
  console.log(options, which);
  publisher = OT.initPublisher('publisher', options, handleError);
  setMode('call');
}

function initTokBox(res) {
  session = OT.initSession(res.apiKey, res.sessionId);
  session.on('streamCreated', function(event) {
    session.subscribe(event.stream, 'subscriber', {
      insertMode: 'append',
      width: '100%',
      height: '100%'
    }, handleError);
  });

  session.connect(res.token, function(error) {
    if(error) {
      handleError(error);
    } else {
      session.publish(publisher, handleError);
    }
  });
}

function endCall() {
  var data =  {
    action: 'end_call'
  }
  jQuery.post(php.ajax_url, data, function(res) {
    res = JSON.parse(res);
    if(res.success) {
      session.disconnect();
      updateCallStatus();
      setMode('contacts');
    }
  });
}

function setCallee(evt) {
  console.log(evt.target.id);
  var callee = jQuery(`#${evt.target.id}`).text();
  jQuery('#contact').text(callee);
  setMode('call-option');
}

/* this is the function that calls create_call
 * in the server side on php, this is a request
 * on demand and it's triggered by clicking on
 * a callee's name */
function initiateCall(evt) {
  var data = {
    callee: jQuery('#contact').text(),
    action: 'create_call'
  };

  initPublisher(evt.target.id);
  jQuery.post(php.ajax_url, data, function(res) {
    res = JSON.parse(res);
    console.log(res);
    initTokBox(res);
  });
}

/* this is the action that calls take_call
 * in the server side on php, it's the request
 * that runs over and over due to long-polling */
function updateCallStatus() {
  var data = {
    action: 'take_call'
  };
  jQuery.post(php.ajax_url, data, function(res) {
    res = JSON.parse(res);
    console.log(res);
    if(res.success) {
      initPublisher();
      initTokBox(res);
    } else {
      setTimeout(updateCallStatus, 5000);
    }
  });
}

(function ($){
  $(document).ready(function() {
    updateCallStatus();
    setMode('contacts');
  });

  $(window).on('beforeunload', function() {
    endCall();
  });
}(jQuery))
