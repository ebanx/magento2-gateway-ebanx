/*browser:true*/
/*global define*/
define(
    [],
    function () {
        return function (elementFinder, callback) {
            var waiter = setInterval(function(){
                var element = elementFinder();
                if (typeof element === 'undefined') {
                    return;
                }

                clearInterval(waiter);
                callback(element);
            }, 500);
        };
    }
);
