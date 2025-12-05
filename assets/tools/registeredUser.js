function registeredUserSelectorSearch(value, accountId, format, page = 1) {
    if (typeof value == undefined) {
        value = '';
    }
    var $container = $('#registeredUserSelectorContent').find('#registredUserResultsContainer'),
        container = $container[0];

    container.innerHTML = `<div class="loading-document">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>`;

    getRegistredUsers(value, accountId, page, function (result) {
        $container.html('');
        $("#registeredUserSelectorContent .order-summary").remove();

        if (result?.data?.data?.items) {
            var $html = getRegistredUserSearchResult(result.data.data.items, accountId, format);
            $container.append($html);
            let pagination = result?.data?.data?.pagination;
            if (pagination && pagination.totalPages > 1) {
                let $pagination = $('<ul class="pagination mt-3"></ul>');
                for (let p = 1; p <= pagination.totalPages; p++) {
                    let $btn = $(`<li class="page-item ${p === pagination.page ? 'current active' : ''}"><a class="item itemLink page-link">${p}</a></li>`);
                    $btn.on('click', function () {
                        registeredUserSelectorSearch(value, accountId, format, p);
                    });
                    $pagination.append($btn);
                }
                $container.append($pagination);
            }
            /*$("#registeredUserSelectorContent").append(`
                <div class="order-summary d-flex justify-content-between align-items-center mt-3 small text-muted">
                    <span>${result.data.data.items.length} usuario${result.data.data.items.length > 1 ? 's' : ''}</span>
                </div>
            `);*/
        }

        $('.selectableRegisteredUser').off('keydown.selectableRegisteredUser').on('keydown.selectableRegisteredUser', function (event) {
            if ($(document.activeElement).is('.selectableRegisteredUser') && (event.keyCode === 13 || event.keyCode === 32)) {
                event.preventDefault();
                $(document.activeElement).click();
            }
        });
    }, function (result) {
        // ajax error
    });
}
function searchRegistredUser(element, accountId, value, format, execute) {
    var $container = $('#registeredUserSelectorContent').find('#registredUserResultsContainer'),
        container = $container[0];

    if (execute) {
        registeredUserSelectorSearch(value, accountId, format, 1);
    } else if (value.length == 0) {
        registeredUserSelectorSearch('', accountId, format, 1);
    } else if (value.length > 2) {
        if (container.timeout) clearTimeout(container.timeout);

        if (container.connect && container.connect.request.readyState != 0 && container.connect.request.readyState != 4)
            searchRegistredUser(element, accountId, value, format, true);
        else
            container.timeout = setTimeout(function () {
                searchRegistredUser(element, accountId, value, format, true);
            }, 400);
    } else {
        container.innerHTML = '';
    }
}
function getRegistredUsers(data, accountId, page, callback, errorCallback) {
    var params = new urlParameterEncoder();
    if (data != "") {
        params.addParameter('data', data);
    }
    params.addParameter('accountId', accountId);
    if (page) {
        params.addParameter('page', page);
    }
    var url = LC.global.routePaths.ACCOUNT_INTERNAL_SEARCH_ACCOUNT_REGISTERED_USER + '/' + params.getParameters();
    $.get(url, callback ? callback : $.noop, 'json').fail(errorCallback ? errorCallback : $.noop);
}
function getRegistredUserSearchResult(items, accountId, format) {
    var html = '';

    if (items.length) {

        $.each(items, function (index, item) {
            html +=
                `<div class="selectableRegisteredUser" 
                        data-item='${JSON.stringify(item)}'
                        data-format='${format}'
                        onclick="selectRegisteredUserResult(this, '${accountId}');">
                    <div class="d-flex flex-row align-items-center gap-3">
                        <div class="bg-light rounded-circle text-center">
                            ${item.image
                    ? `<img src="${item.image}" class="selectableRegisteredUserImg" alt="registered user image" >`
                    : `<svg class="icon icon-action selectableRegisteredUserImg"><use xlink:href="#icon-user"></use></svg>`
                }
                        </div>
                        <div>
                            <div class="fw-semibold">${item.firstName} ${item.lastName}</div>
                            <div class="text-muleted small">${item.email}</div>
                            <div class="d-flex fx-row flex-wrap">
                                <div class="text-muted small">${item.username ? item.username : 'N/A'}</div>
                                <div class="mx-1">•</div>
                                <div class="text-muted small">ID: ${item.pId}</div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-end justify-content-center text-nowrap">
                        <div class="text-muted">${formatDate(item.dateAdded, format)}</div>
                        <small class="text-muted">${LC.global.languageSheet.accountRegisteredUserSearchLastUsedActive}: ${formatLastUsed(item.lastUsed)}</small>
                    </div>
                </div>`;
        });

    } else {
        html += `<div class="notFound">${LC.global.languageSheet.locationNotFound}</div>`;
    }
    return html;
}
function formatDate(date, format) {
    if (!(date instanceof Date)) {
        date = new Date(date);
    }

    if (isNaN(date)) {
        return "";
    }

    const yyyy = date.getFullYear();
    const mm = String(date.getMonth() + 1).padStart(2, '0');
    const dd = String(date.getDate()).padStart(2, '0');
    const hh = String(date.getHours()).padStart(2, '0');
    const min = String(date.getMinutes()).padStart(2, '0');
    const ss = String(date.getSeconds()).padStart(2, '0');

    return format
        .replace(/YYYY/g, yyyy)
        .replace(/MM/g, mm)
        .replace(/DD/g, dd)
        .replace(/HH/g, hh)
        .replace(/mm/g, min)
        .replace(/ss/g, ss);
}
function formatLastUsed(lastUsedDate) {
    if (!lastUsedDate) return LC.global.languageSheet.accountRegisteredUserSearchLastUsedNever;

    var now = new Date();
    var date = new Date(lastUsedDate);
    var diffMs = now - date;

    if (isNaN(date) || diffMs < 0) return LC.global.languageSheet.accountRegisteredUserSearchLastUsedNever;

    var diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

    if (diffDays === 0) return LC.global.languageSheet.accountRegisteredUserSearchLastUsedToday;
    if (diffDays === 1) return LC.global.languageSheet.accountRegisteredUserSearchLastUsedDayAgo.replace("{{day}}", diffDays);
    return LC.global.languageSheet.accountRegisteredUserSearchLastUsedDaysAgo.replace("{{day}}", diffDays);
}
function selectRegisteredUserResult(element, accountId) {
    $('#registeredUserSelectorContainer #registeredUserSelector').addClass('d-none');
    $('#registeredUserSelectorContainer #registeredUserSelect').prop('disabled', true);
    $('#registeredUserSelectorContainer #selectedRegisteredUserSummary').html('<div class="loading-document"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
    $('#registeredUserSelectorContainer #selectedRegisteredUserSummary').removeClass('d-none');

    var item = $(element).data('item');
    var format = $(element).data('format');

    if (typeof item !== 'undefined' && item !== null) {
        var html =
            `
            <div class="d-flex flex-row align-items-center justify-content-between">
                <div class="d-flex flex-row align-items-center gap-3">
                    <div class="bg-light rounded-circle text-center">
                        ${item.image ? `<img class="selectableRegisteredUserImg" src="${item.image}">` : '<svg class="icon icon-action selectableRegisteredUserImg"><use xlink:href="#icon-user"></use></svg>'}
                    </div>
                        <div>
                        <div class="fw-semibold">${item.firstName} ${item.lastName}</div>
                        <div class="text-muleted small">${item.email}</div>
                        <div class="d-flex fx-row flex-wrap">
                            <div class="text-muted small">${item.username ? item.username : 'N/A'} </div>
                            <div class="mx-1">•</div>
                            <div class="text-muted small">ID: ${item.pId}</div>
                    </div>
                    </div>
                </div>
                <div class="d-flex flex-column align-items-end justify-content-center text-nowrap">
                    <button class="btn btn-secondary" onclick="resetRegisteredUserSelector(event, '${accountId}', '${format}')">Volver a elegir</button>
                </div>
            </div>
            `
            ;
        $('#selectedRegisteredUserSummary').html(html);
        $('#registeredUserSelectorContainer #registeredUserId').val(item.id);
    }
}
function resetRegisteredUserSelector(event, accountId, format) {
    event.preventDefault();
    $('#registeredUserSelector').removeClass('d-none');
    $('#registeredUserSelect').prop('disabled', false);
    $('#selectedRegisteredUserSummary').addClass('d-none');
    $('#selectedRegisteredUserSummary').html('');
    $('#registeredUserSelectorContainer #registeredUserId').val("");
    registeredUserSelectorSearch("", accountId, format, 1);
}
function initCalendar(form, dateFormat = 'YYYY-MM-DDTHH:mm:ssZ') {
    $(form).find('[data-datetimepicker]').each(
        (index, el) => {
            var $calendar = $(el),
                language = $calendar.data('language') ? $calendar.data('language') : 'en',
                format = $calendar.data('format'),
                startDate = $calendar.data('startdate'),
                endDate = $calendar.data('enddate'),
                weekstart = $calendar.data('weekstart');
            moment.locale(language, {
                week: { dow: weekstart }
            });
            $calendar.datetimepicker({
                locale: language,
                format: format ? format : CALENDAR_PLUGIN_DATE_FORMAT,
                minDate: startDate ? startDate : false,
                maxDate: endDate ? endDate : false
            });

            $(el).on('dp.change', (e) => {
                var $optionsubmitValue = $(form).find('[name="' + $calendar.data('submit') + '"]');
                if (e.date) {
                    $optionsubmitValue.val(moment(e.date).format(dateFormat));
                } else {
                    $optionsubmitValue.val('');
                }
            });

        }
    );
}

function accountRegisteredUsersReloadResults(urlPath) {
    var $content = $('#accountRegisteredUsersReload');
    if ($content.length === 0) {
        window.location.href = urlPath;
    } else {
        $content.html(DEFAULT_LOADING_SPINNER);
        $("#accountRegisteredUsersLoadUrl").val(urlPath);
        $content.load(urlPath + ' #accountRegisteredUsersReload > *', function (response, status) {
            if ($("body.lcContent-accountRegisteredUsers").length > 0) {
                history.pushState({ url: urlPath }, '', urlPath);
            }
            $content.find('.lc-accountRegisteredUsersModal').remove();
            $content.find('[data-lc-event]').dataEvent();
        });
    }

}

function accountOrdresReloadResults(urlPath) {
    var $content = $('#accountOrdresReload');
    if ($content.length === 0) {
        window.location.href = urlPath;
    } else {
        $content.html(DEFAULT_LOADING_SPINNER);
        $("#accountOrdersLoadUrl").val(urlPath);
        $content.load(urlPath + ' #accountOrdresReload > *', function (response, status) {
            if ($("body.lcContent-accountOrders").length > 0) {
                history.pushState({ url: urlPath }, '', urlPath);
            }
            $content.find('.lc-accountOrdersModal').remove();
            $content.find('[data-lc-event]').dataEvent();
        });
    }
}

function companyRolesReloadResults(urlPath) {
    var $content = $('#companyRolesReload');
    if ($content.length === 0) {
        window.location.href = urlPath;
    } else {
        $content.html(DEFAULT_LOADING_SPINNER);
        $("#companyRolesLoadUrl").val(urlPath);
        $content.load(urlPath + ' #companyRolesReload > *', function (response, status) {
            if ($("body.lcContent-accountCompanyRoles").length > 0) {
                history.pushState({ url: urlPath }, '', urlPath);
            }
            $content.find('.lc-companyRolesModal').remove();
            $content.find('[data-lc-event]').dataEvent();
        });
    }
}

function closeRegisteredUserModalAndReload($modalClose) {
    $modalClose.click();
    var newUrl = $("#accountRegisteredUsersLoadUrl").val();
    accountRegisteredUsersReloadResults(newUrl);
}

function closeAccountOrdresReloadResults($modalClose) {
    $modalClose.click();
    var newUrl = $("#accountOrdersLoadUrl").val();
    accountOrdresReloadResults(newUrl);
}

function closeCompanyRolesReloadResults($modalClose) {
    $modalClose.click();
    var newUrl = $("#companyRolesLoadUrl").val();
    companyRolesReloadResults(newUrl);
}