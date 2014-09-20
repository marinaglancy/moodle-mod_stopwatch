M.mod_stopwatch = {};

M.mod_stopwatch.init = function(Y, cmid) {

    var lastresumed = null;
    var clocktimer;
    var totalduration = 0;

    var pad = function(num, len) {
        return ("000000000" + num).substr(-len, len);
    }
    var settimer = function(ms) {
        var st = ms%1000,
            s = (ms - st)/1000%60,
            m = ((ms - st)/1000 - s)/60%60,
            h = (((ms - st)/1000 - s)/60 - m)%60;
        Y.one('#stopwatchform').one('#clock').set('value', 
            pad(h,2)+':'+pad(m,2)+':'+pad(s,2));//+'.'+pad(st,3));
    }
    var updatetimer = function() {
        settimer(totalduration + (new Date().getTime()) - lastresumed);
        clocktimer = setTimeout(updatetimer, 1);
    }
    var resumetimer = function() {
        if (Y.one('#stopwatchform').hasClass('running')) {
            return;
        }
        lastresumed = new Date().getTime();
        Y.one('#stopwatchform').addClass('running')
        Y.one('#stopwatchform').addClass('started')
        clocktimer = setTimeout(updatetimer, 10);
    }
    var pausetimer = function() {
        if (!Y.one('#stopwatchform').hasClass('running')) {
            return;
        }
        clearTimeout(clocktimer);
        totalduration += (new Date().getTime()) - lastresumed;
        Y.one('#stopwatchform').removeClass('running')
    }
    var resettimer = function() {
        pausetimer();
        totalduration = 0;
        settimer(0);
        Y.one('#stopwatchform').removeClass('started')
    }

    Y.one('#stopwatchform').one('#start').on('click', resumetimer);
    Y.one('#stopwatchform').one('#resume').on('click', resumetimer);
    Y.one('#stopwatchform').one('#pause').on('click', pausetimer);
    Y.one('#stopwatchform').one('#reset').on('click', resettimer);
    settimer(0);
}