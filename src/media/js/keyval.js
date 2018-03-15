(function($) {
    var resetButtons, addF, subF, upF, downF, moveF;
    // we have to have a function to reset the buttons
    resetButtons = function(elem) {
        var addButtons = $(elem).find('.jinboundkeyval_add');
        var subButtons = $(elem).find('.jinboundkeyval_sub');
        var upButtons = $(elem).find('.jinboundkeyval_up');
        var downButtons = $(elem).find('.jinboundkeyval_down');
        // NOTE: the following code uses setTimeout when adding events so they work in 3.x
        if (addButtons) {
            $.each(addButtons, function(idx, el) {
                $(el).off('click');
                setTimeout(function() {
                    $(el).on('click', addF);
                }, 5);
            });
        }
        if (subButtons) {
            $.each(subButtons, function(idx, el) {
                $(el).off('click');
                setTimeout(function() {
                    $(el).on('click', subF);
                }, 5);
            });
        }
        if (upButtons) {
            $.each(upButtons, function(idx, el) {
                $(el).removeAttr('disabled');
                $(el).off('click');
                setTimeout(function() {
                    $(el).on('click', upF);
                }, 5);
                // we have to use index 1 and not 0 as the hidden block is 0
                if (1 == idx) {
                    $(el).attr('disabled', 'disabled');
                }
            });
        }
        if (downButtons) {
            $.each(downButtons, function(idx, el) {
                $(el).removeAttr('disabled');
                $(el).off('click');
                setTimeout(function() {
                    $(el).on('click', downF);
                }, 5);
                if (downButtons.length - 1 == idx) {
                    $(el).attr('disabled', 'disabled');
                }
            });
        }
    };
    addF = function(ev) {
        var hasEmpty = false, p, t, b, inputBlocks, range, documentFragment;
        try {
            // get the main parent block
            p = $(ev.target.parentNode.parentNode.parentNode);
            // the template for adding a new block
            t = p.find('.jinboundkeyval_default').first();
            // the main block
            b = p.find('.jinboundkeyval_stage').first();
            // check our inputs to see if we have empties
            // note that both key AND value must be empty!
            inputBlocks = b.find('.jinboundkeyval_inputs');
            if (inputBlocks.length) {
                $.each(inputBlocks, function(idx, el) {
                    if (hasEmpty) {
                        return;
                    }
                    var i = $(el).find('input');
                    if ('' === $(i[0]).val() && '' === $(i[1]).val()) {
                        hasEmpty = true;
                    }
                });
            }
            if (hasEmpty) {
                alert(Joomla.JText._('COM_JINBOUND_JINBOUNDKEYVAL_EMPTY'));
                return false;
            }
            // create a new range to append
            range = document.createRange();
            // NOTE: cannot use jquery objects here!
            if ('undefined' != typeof t.jquery) t = t[0];
            if ('undefined' != typeof b.jquery) b = b[0];
            range.selectNode(t);
            documentFragment = range.createContextualFragment(t.innerHTML.toString());
            b.appendChild(documentFragment);
            resetButtons(p);
        } catch (err) {
            alert(err);
            return false;
        }
    };
    subF = function(ev) {
        try {
            // get the main parent block
            var p = $(ev.target.parentNode);
            if (p.length) {
                var c = p.parent().children();
                if (1 < c.length) {
                    p.empty().remove();
                }
                else {
                    alert(Joomla.JText._('COM_JINBOUND_JINBOUNDKEYVAL_EMPTY_REMOVE'));
                }
            }
        } catch (err) {
            alert(err);
            return false;
        }
    };
    moveF = function(ev, dir) {
        try {
            // parent block, target sibling
            var p = $(ev.target.parentNode), t, m;
            switch (dir) {
                case 'before':
                    t = $(p).prev('.jinboundkeyval_block');
                    m = 'insertBefore';
                    break;
                case 'after':
                    t = $(p).next('.jinboundkeyval_block');
                    m = 'insertAfter';
                    break;
                default:
                    return false;
            }
            if (t.length) {
                $(p)[m](t);
                resetButtons($(ev.target.parentNode.parentNode.parentNode));
                return true;
            }
            return false;
        }
        catch (err) {
            return false;
        }
    };
    upF = function(ev) {
        return moveF(ev, 'before');
    };
    downF = function(ev) {
        return moveF(ev, 'after');
    };
    // everything starts here
    $(document).ready(function() {
        // we want this to run for each instance
        var fields = $('.jinboundkeyval');
        if (!fields.length) {
            alert(Joomla.JText._('COM_JINBOUND_JINBOUNDKEYVAL_ERROR'));
            return;
        }
        // loop over each of our fields (there should only be one, but hey! who knows?)
        for (var i = 0; i < fields.length; i++) {
            resetButtons(fields[i]);
        }
    });
})(jQuery);
