(function($) {
    $(document).ready(function() {
        var modes = ['', 'c1_', 'c2_', 'c3_'];

        var getModeElementId = function(type, alt) {
            return 'jform_params_' + alt + 'mode_' + type;
        };

        var isUndefined = function(what) {
            return 'undefined' === typeof what;
        };

        $.each(modes, function(i, mode) {
            var select = $('#jform_params_' + mode + 'mode');
            if (!isUndefined(select)) {
                select.change(function(e) {
                    var enabled = getModeElementId($(this).find(':selected').val(), mode);
                    if (isUndefined(enabled)) return;
                    $.each($(this).find('option'), function(idx, el) {
                        var current = getModeElementId($(el).val(), mode);
                        var elem = $('#' + current);
                        if (isUndefined(elem)) return;
                        var cg = elem.closest('.control-group');
                        if (0 == cg.length) {
                            cg = elem.closest('li');
                        }
                        if (current === enabled) {
                            cg.show();
                        }
                        else {
                            cg.hide();
                        }
                    });
                }).trigger('change').trigger('liszt:updated');
            }
            var c_enabled = $('#jform_params_' + mode + 'enabled');
            if (!isUndefined(c_enabled)) {
                c_enabled.find('input').change(function(e) {
                    var fields = ['match', 'conditions', 'mode', 'mode_modules', 'mode_module', 'mode_editor'], action;
                    if (1 == $(this).val()) {
                        action = 'show';
                    }
                    else {
                        action = 'hide';
                    }
                    $.each(fields, function(fidx, fel) {
                        var field = $('#jform_params_' + mode + '' + fel), cg;
                        if (field.length) {
                            cg = field.closest('.control-group');
                            if (0 == cg.length) {
                                cg = field.closest('li');
                            }
                            cg[action]();
                        }
                    });
                    if ('show' == action && !isUndefined(select)) {
                        select.trigger('change');
                    }
                });
                $.each(c_enabled.find('input'), function(iidx, iel) {
                    if ($(iel).prop('checked')) $(iel).trigger('change');
                });
            }
            if (mode.length) {
                var c_editors = $('#jform_params_' + mode + 'mode_editor');
                $.each(c_editors, function(eidx, eid) {
                    var cg = c_editors.closest('.control-group');
                    var hr = $('<hr>');
                    if (cg.length) {
                        cg.after(hr);
                    }
                });
            }
        });

        $(document.body).on('click', 'button.mod_jinbound_cta_conditions_del', function() {
            $(this).closest('.control-group').empty().remove();
        });

    });

    window.ModJInboundCTAConditionURLs = [];
    window.ModJInboundCTAConditionInputs = 0;
    window.ModJInboundCTACondition = function(id, init) {
        var block = $('#' + id), controls = $('#' + id + '_controls'), buttons = $('#' + id + '_buttons button'),
            renderElem = function(html) {
                var repl = '$1$2_' + window.ModJInboundCTAConditionInputs + '$3';
                html = html.replace(/(\sid=\")(.*?)(\")/gm, repl).replace(/(\sfor=\")(.*?)(\")/gm, repl);
                return html;
            };
        block.css('position', 'relative');
        if ('LI' == block.parent().prop("tagName")) {
            block.css('float', 'left');
        }
        $('<div id="' + id + '_loading"></div>')
            .css("background", "rgba(255, 255, 255, .8) url('../media/jui/img/ajax-loader.gif') 50% 15% no-repeat")
            .css("top", 0)
            .css("left", 0)
            .css("width", block.width())
            .css("height", Math.max(block.height(), 64))
            .css("position", "absolute")
            .css("opacity", "0.80")
            .css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity = 80)")
            .css("filter", "alpha(opacity = 80)")
            .css("display", "none")
            .insertBefore(controls);
        $.each(buttons, function(idx, el) {
            $(el).click(function(e) {
                $('#' + id + '_loading')
                    .css("width", block.width())
                    .css("height", block.height())
                    .css('display', 'block');
                var field = $(this).attr('data-field'), label = $(this).attr('data-label'),
                    desc                                      = $(this).attr('data-desc'), empty = $(this).attr('data-empty'),
                    name                                      = $(this).attr('data-name'), def     = $(this).attr('data-default'),
                    group                                     = $(this).attr('data-group'), yesno = $(this).attr('data-default-yesno'),
                    url                                       = window.ModJInboundCTAConditionURL + '',
                    cached                                    = false, err                       = function(jqXHR, textStatus, errorThrown) {
                        if (Joomla.JText._('COM_AJAX_MODULE_NOT_ACCESSIBLE').toString().replace(/\%s/, 'mod_jinbound_cta') === textStatus) {
                            alert(Joomla.JText._('MOD_JINBOUND_CTA_PUBLISH_AND_SAVE_FIRST'));
                        }
                        else {
                            alert(textStatus);
                        }
                        $('#' + id + '_loading').css('display', 'none');
                    },
                    is                                        = function(what) {
                        return 'undefined' !== typeof what && what.length;
                    },
                    doButton                                  = function(target) {
                        target.find(".btn-group label").each(function() {
                            var label = $(this);
                            var input = $('#' + label.attr('for'));
                            var rname = input.attr('name');
                            if (!rname.match(/\[\]$/)) {
                                input.attr('name', rname + '[' + window.ModJInboundCTAConditionInputs + ']');
                            }
                        });
                        target.find(".btn-group label:not(.active)").click(function() {
                            var label = $(this);
                            var input = $('#' + label.attr('for'));
                            if (!input.prop('checked')) {
                                label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
                                if (input.val() == '') {
                                    label.addClass('active btn-primary');
                                } else if (input.val() == 0) {
                                    label.addClass('active btn-danger');
                                } else {
                                    label.addClass('active btn-success');
                                }
                                input.prop('checked', true);
                            }
                        });
                        target.find(".btn-group input[checked=checked]").each(function() {
                            console.log($(this));
                            if ($(this).val() == '') {
                                $("label[for=" + $(this).attr('id') + "]").addClass('active btn-primary');
                            } else if ($(this).val() == 0) {
                                $("label[for=" + $(this).attr('id') + "]").addClass('active btn-danger');
                            } else {
                                $("label[for=" + $(this).attr('id') + "]").addClass('active btn-success');
                            }
                        });
                    },
                    success                                   = function(data, textStatus, jqXHR) {
                        $('#' + id + '_loading').css('display', 'none');
                        if (!(data && data.success)) {
                            err(false, data.message);
                            return;
                        }
                        var div                                                      = function(cls) {
                                return $('<div class="' + cls + '"></div>');
                            },
                            cg = div('control-group'), cl = div('control-label'), cc = div('controls'),
                            c = div('input-append input-prepend'),
                            i                                                        = $(renderElem(data.data.field.field)), l = $(renderElem(data.data.field.label)),
                            del                                                      = $('<button type="button" class="btn mod_jinbound_cta_conditions_del jgrid"> <span class="icon-minus state trash"> </span> </button>'),
                            cmp                                                      = ('jinboundcampaignlist' === field), r              = false, n = i.attr('name'),
                            isnew                                                    = ('isnew' === name),
                            rcached                                                  = false,
                            u;
                        window.ModJInboundCTAConditionInputs += 1;
                        if (cmp) {
                            u = window.ModJInboundCTAConditionURL + '&type=radio&label=x&desc=x&name=' + name + '_yesno&options[0][text]=JYES&options[0][value]=1&options[1][text]=JNO&options[1][value]=0&class=radio%20btn-group%20btn-group-yesno%20mod_jinbound_cta_campaign_toggle&default=1' + (is(group) ? '&group=' + group : '');
                        }
                        else if (isnew) {
                            u = false;
                        }
                        else {
                            u = window.ModJInboundCTAConditionURL + '&type=jinboundcampaignlist&label=x&desc=x&name=' + name + '_campaign&options[0][text]=MOD_JINBOUND_CTA_ANY_CAMPAIGN&options[0][value]=' + (is(group) ? '&group=' + group : '');
                        }
                        if (u) {
                            $.each(window.ModJInboundCTAConditionURLs, function(idx, el) {
                                if (el.url === u) {
                                    rcached = el;
                                }
                            });
                            if (false !== rcached) {
                                r = $(renderElem(rcached.data.data.field));
                                window.ModJInboundCTAConditionInputs += 1;
                            }
                            else {
                                $.ajax({
                                    url       : u
                                    , async   : false
                                    , dataType: 'json'
                                    , error   : err
                                    , success : function(rdata, rtextStatus, rjqXHR) {
                                        if (!(rdata && rdata.success)) {
                                            err(false, rdata.message);
                                            return;
                                        }
                                        r = $(renderElem(rdata.data.field.field));
                                        window.ModJInboundCTAConditionURLs.push({url: u, data: rdata.data});
                                        window.ModJInboundCTAConditionInputs += 1;
                                    }
                                });
                            }
                            if (!n.match(/\[\]$/)) {
                                i.attr('name', n + '[' + window.ModJInboundCTAConditionInputs + ']');
                            }
                        }
                        else {
                            $.each(i.find('input'), function(iidx, iel) {
                                var iname = $(iel).attr('name');
                                $(iel).attr('name', iname + '[' + window.ModJInboundCTAConditionInputs + ']')
                            });
                            $.each(i.find('label'), function(lidx, lel) {
                                $(lel).addClass('btn');
                            });
                        }
                        cg.appendTo(controls);
                        cl.appendTo(cg);
                        cc.appendTo(cg);
                        c.appendTo(cc);
                        del.appendTo(c);
                        i.appendTo(c);
                        l.appendTo(cl);
                        if (r && r.length) {
                            r.appendTo(cc);
                            doButton(cc);
                            if (cmp) {
                                r.find('label').addClass('btn');
                                try {
                                    r.button();
                                }
                                catch (err) {
                                    console.log(err);
                                }
                                if (is(yesno)) {
                                    var cid = cc.find(".btn-group input[value=" + yesno + "]").attr('id');
                                    cc.find('.btn-group label[for=' + cid + ']').trigger('click');
                                    console.log(cc.find('.btn-group label[for=' + cid + ']'));
                                }
                            }
                            else {
                                var rname = r.attr('name');
                                if (!rname.match(/\[\]$/)) {
                                    r.attr('name', rname + '[' + window.ModJInboundCTAConditionInputs + ']');
                                }
                                if (is(yesno)) {
                                    r.val(yesno);
                                }
                            }
                        }
                        if (isnew) {
                            doButton(cc);
                            console.log('isnew');
                            console.log(cc.find(".btn-group label").first());
                            cc.find(".btn-group label").first().trigger('click');
                            console.log(cc.find(".btn-group input").first().prop('checked'));
                        }
                        window.ModJInboundCTAConditionURLs.push({url: url, data: data});
                    }
                ;
                if (is(field)) url += '&type=' + field;
                if (is(group)) url += '&group=' + group;
                if (is(label)) url += '&label=' + label;
                if (is(desc)) url += '&desc=' + desc;
                if (is(name)) url += '&name=' + name;
                if (is(name) && 'isnew' === name) url += '&options[0][text]=JYES&options[0][value]=1&options[1][text]=JNO&options[1][value]=0';
                else if (is(empty)) url += '&options[0][text]=' + empty + '&options[0][value]=';

                $.each(window.ModJInboundCTAConditionURLs, function(idx, el) {
                    if (el.url === url) {
                        cached = el;
                    }
                });
                if (false !== cached) {
                    success(cached.data, false, false);
                }
                else {
                    $.ajax({
                        url       : url
                        , async   : false
                        , dataType: 'json'
                        , error   : err
                        , success : success
                    });
                }
                if (is(def)) {
                    var group = controls.find('.control-group:last-child');
                    if (is(name) && 'isnew' === name) {
                        var nid = group.find(".btn-group input[value=" + def + "]").attr('id');
                        group.find('.btn-group label[for=' + nid + ']').trigger('click');
                    }
                    else {
                        group.find('select').first().val(def);
                    }
                }
            });
        });
        if (init) {
            console.log(init);
            var cb;
            if (init.priority && init.priority_campaign) {
                cb = $('#' + id + '_buttons button[data-name="priority"]');
                $.each(init.priority, function(idx, el) {
                    console.log('Setting "' + id + '" to "' + el + '", yesno to "' + init.priority_campaign[idx] + '"');
                    cb.attr('data-default', el);
                    cb.attr('data-default-yesno', init.priority_campaign[idx]);
                    cb.trigger('click');
                    cb.removeAttr('data-default');
                    cb.removeAttr('data-default-yesno');
                });
            }
            if (init.status && init.status_campaign) {
                cb = $('#' + id + '_buttons button[data-name="status"]');
                $.each(init.status, function(idx, el) {
                    console.log('Setting "' + id + '" to "' + el + '", yesno to "' + init.status_campaign[idx] + '"');
                    cb.attr('data-default', el);
                    cb.attr('data-default-yesno', init.status_campaign[idx]);
                    cb.trigger('click');
                    cb.removeAttr('data-default');
                    cb.removeAttr('data-default-yesno');
                });
            }
            if (init.campaign && init.campaign_yesno) {
                cb = $('#' + id + '_buttons button[data-name="campaign"]');
                $.each(init.campaign, function(idx, el) {
                    console.log('Setting "' + id + '" to "' + el + '", yesno to "' + init.campaign_yesno[idx] + '"');
                    cb.attr('data-default', el);
                    cb.attr('data-default-yesno', init.campaign_yesno[idx]);
                    cb.trigger('click');
                    cb.removeAttr('data-default');
                    cb.removeAttr('data-default-yesno');
                });
            }
            if (init.isnew) {
                cb = $('#' + id + '_buttons button[data-name="isnew"]');
                for (var prop in init.isnew) {
                    if (prop.match(/^[0-9]*$/)) {
                        console.log('Setting "' + id + '" to "' + init.isnew[prop][prop] + '"');
                        cb.attr('data-default', init.isnew[prop][prop]);
                        cb.trigger('click');
                        cb.removeAttr('data-default');
                    }
                }
            }
        }
    };
})(jQuery);
