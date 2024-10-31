import { isMobile } from 'mobile-device-detect';

const ProjectAdvInserter = {
    slots: isMobile ? 
        window.projectadvinserter.slots.filter(slot => slot.is_mobile) : 
        window.projectadvinserter.slots.filter(slot => !slot.is_mobile),
    init: function() {
        if (this.slots.length > 0) {
            this.insert_slot(this.slots.pop());
        }
    },
    insert_slot: function(slot) {
        const element = document.querySelector(slot.selector);
        if (element) {
            let position = '';
            if (slot.position === 'after') {
                position = 'afterend';
            }
            if (slot.position === 'before') {
                position = 'beforebegin';
            }
            element.insertAdjacentHTML(position, slot.content);
        }
        if (this.slots.length > 0) {
            this.insert_slot(this.slots.pop());
        }
    }
}
ProjectAdvInserter.init();