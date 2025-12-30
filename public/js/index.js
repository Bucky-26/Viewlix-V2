// index.js
// Homepage interactive helpers: carousel swipe/pointer, clickable cards, keyboard support.

(function(){
    // Basic swipe and pointer s
    // upport for hs-carousel
    document.querySelectorAll('.hs-carousel').forEach(function(carousel){
        var startX = 0;
        var startTime = 0;
        var threshold = 50; // px needed to qualify as swipe
        var allowedTime = 1000; // max time allowed to take for a swipe (ms)

        // Touch events (mobile)
        carousel.addEventListener('touchstart', function(e){
            if(e.touches.length !== 1) return;
            startX = e.touches[0].clientX;
            startTime = Date.now();
        }, {passive:true});

        carousel.addEventListener('touchend', function(e){
            var dx = e.changedTouches[0].clientX - startX;
            var dt = Date.now() - startTime;
            if(Math.abs(dx) >= threshold && dt <= allowedTime){
                if(dx < 0){ // swipe left -> next
                    var next = carousel.querySelector('.hs-carousel-next');
                    if(next) next.click();
                } else { // swipe right -> prev
                    var prev = carousel.querySelector('.hs-carousel-prev');
                    if(prev) prev.click();
                }
            }
        }, {passive:true});

        // Pointer events (desktop dragging / pen)
        var pointerActive = false;
        var pointerId = null;
        carousel.addEventListener('pointerdown', function(e){
            // only left button or primary pointer
            if(e.isPrimary === false) return;
            pointerActive = true;
            pointerId = e.pointerId;
            startX = e.clientX;
            startTime = Date.now();
            try{ carousel.setPointerCapture(pointerId); }catch(err){}
        });

        carousel.addEventListener('pointerup', function(e){
            if(!pointerActive || e.pointerId !== pointerId) return;
            pointerActive = false;
            var dx = e.clientX - startX;
            var dt = Date.now() - startTime;
            if(Math.abs(dx) >= threshold && dt <= allowedTime){
                if(dx < 0){ carousel.querySelector('.hs-carousel-next')?.click(); }
                else { carousel.querySelector('.hs-carousel-prev')?.click(); }
            }
            try{ carousel.releasePointerCapture(pointerId); }catch(err){}
        });

        // If the carousel auto-plays, prevent accidental triggers when a user is scrolling vertically
        // We use a small check on the touchmove to detect primarily-horizontal movement.
        var moved = false;
        var lastMoveX = 0;
        carousel.addEventListener('touchmove', function(e){
            if(e.touches.length !== 1) return;
            var moveX = e.touches[0].clientX;
            var moveY = e.touches[0].clientY;
            var dx = Math.abs(moveX - startX);
            // if horizontal movement exceeds small amount, prevent default to avoid page-snap on some devices
            if(dx > 10){
                moved = true;
                lastMoveX = moveX;
            }
        }, {passive:true});
    });

    // Attach click handlers directly to each card — more reliable for mouse events and avoids
    // interference from other global handlers.
    document.querySelectorAll('.viewlix-clickable-card').forEach(function(card){
        card.addEventListener('click', function(e){
            // If the click originated from a real interactive element, let it proceed
            if(e.target.closest && e.target.closest('a, button, input')) return;
            var url = card.getAttribute('data-url');
            if(url) window.location.href = url;
        });
    });

    // keyboard support: Enter or Space activates the card when focused
    document.addEventListener('keydown', function(e){
        var el = document.activeElement;
        if(!el || !el.classList.contains('viewlix-clickable-card')) return;
        if(e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            var url = el.getAttribute('data-url');
            if(url) window.location.href = url;
        }
    });
})();
