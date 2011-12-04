/**
 * A jQuery plugin to create a simple timed sliding carousel without added UI.
 * (You could hookup your own UI elements using the API though)
 *
 * This carousel is based loosely on Remy Sharps' infiniteCarousel
 * @see http://jqueryfordesigners.com/automatic-infinite-carousel/
 *
 * we need three types of element:
 * .Carousel the base element everything happens on.
 *   .CarouselContentWrapper (1x, immediately inside the .Carousel)
 *      .CarouselContent (one for each `page` of content you want to display)
 *
 * The MIT License (MIT)
 * =====================
 * Copyright (c) 2011 Kris Herlaar <kris@theredhead.nl>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this
 * software and associated documentation files (the "Software"), to deal in the Software
 * without restriction, including without limitation the rights to use, copy, modify, merge,
 * publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons
 * to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or
 * substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE
 * FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 */
(function () {
    $.fn.carousel = function (options) {

        var settings = $.extend({
             autoSlideDelay: 5000
            ,animationSpeed: 500
            ,animationEasing: 'swing'
        }, options);

//        autoSlideDelay = typeof autoSlideDelay == 'Number' ? autoSlideDelay : 5000;

        return this.each(function () {

            var $this = $(this);
            var $wrapper = ('.CarouselContentWrapper', $this);
            var width = $this.innerWidth();
            var height = $this.innerHeight();
            var currentIndex = 0;
            var $items = $('.CarouselContent', $this);
            var mouseOver = false;
            var $timer;

            // stand still when the mouse is over the element.
            $this.hover(function(){
                mouseOver = true;
            }, function(){
                mouseOver = false;
            });

            // set to 0;
            $this.scrollLeft(0);

            heartbeat = function () {
                if (!mouseOver) {
                    slideTo(currentIndex + 1);
                }
            }

            /**
             * set the timer.
             */
            $timer = setInterval(heartbeat, settings.autoSlideDelay);

            /**
             * scroll to a specific index
             *
             * @param index
             */
            function slideTo(index) {
                clearInterval($timer);
                // perform wrapping when index is out of bounds
                index = index > $items.length ? 1 : index;
                index = index < 1 ? $items.length : index;

                nextLeft = (index-1) * width;
//                console.log(($this.attr('id') + ' currentIndex, next: '), currentIndex, index);

                currentIndex = index;
                $wrapper.stop().animate({scrollLeft : nextLeft + 'px'}
                    , settings.animationSpeed
                    , settings.animationEasing
                    , function () {
                        $timer = setInterval(heartbeat, settings.autoSlideDelay);
                    });
            }

            /**
             * Go to the next slide
             */
            function goNext() {
                slideTo(currentIndex + 1);
            }

            /**
             * Go to the previous slide
             */
            function goPrevious() {
                slideTo(currentIndex - 1);
            }

            /**
             * Setup so the first transition won't take double time.
             */
            slideTo(1);

            $this.click(function(ev){

//                console.log('x, y: ', ev.offsetX, ev.offsetY);

                if (ev.offsetX <= width / 2) {
                    goPrevious();
                } else {
                    goNext();
                }
            });
        });
    };
})(jQuery);