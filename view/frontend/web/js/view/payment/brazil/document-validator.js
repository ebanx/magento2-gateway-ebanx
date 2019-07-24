define(
    function () {
        'use strict';
        return function (document_text) {
            const number = document_text.replace(/[^\d]/g, "");

            if(number.length === 11){

                const r = /^(0{11}|1{11}|2{11}|3{11}|4{11}|5{11}|6{11}|7{11}|8{11}|9{11})$/;
                let sum = 0;
                let remainder;
              
                if (r.test(number)) return false;
              
                for (let i = 1; i <= 9; i++) sum += (number.charAt(i - 1, i) * (11 - i)); //eslint-disable-line
              
                remainder = (sum * 10) % 11;
              
                if ((remainder === 10) || (remainder === 11)) remainder = 0;
                if (remainder.toString() !== number.charAt(9)) return false;
              
                sum = 0;
                for (let i = 1; i <= 10; i++) sum += number.charAt(i - 1, i) * (12 - i); //eslint-disable-line
              
                remainder = (sum * 10) % 11;
              
                if ((remainder === 10) || (remainder === 11)) remainder = 0;
                if (remainder.toString() !== number.charAt(10)) return false;
                return true;               
            }

            if(number.length === 14){
                const r = /^(0{14}|1{14}|2{14}|3{14}|4{14}|5{14}|6{14}|7{14}|8{14}|9{14})$/;
                if (r.test(number)) return false;

                let remainder = 0;
                let size = number.length - 2;
                let numbers = number.substring(0, size);
                let pos = size - 7;
                const sum = 0;
                const digits = number.substring(size);


                const loop = (one, two, three, four) => {
                    for (let i = one; i >= 1; i--) { //eslint-disable-line
                    two += three.charAt(one - i) * four--; //eslint-disable-line
                    if (four < 2) four = 9; //eslint-disable-line
                    }

                    return (two % 11 < 2 ? 0 : 11 - (two % 11)).toString();
                };

                remainder = loop(size, sum, numbers, pos);
                if (remainder !== digits.charAt(0)) return false;

                size += 1;
                numbers = number.substring(0, size);
                pos = size - 7;

                remainder = loop(size, sum, numbers, pos);
                if (remainder !== digits.charAt(1)) return false;

                return true;
            }

            return false;
        };
    }
);
