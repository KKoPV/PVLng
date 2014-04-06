<script>
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
</script>

<script src="/js/jquery.treetable.js"></script>

<script>

/**
 *
 */
$(function() {

    $.fn.dataTableExt.oPagination.listbox = {
        /*
         * Function: oPagination.listbox.fnInit
         * Purpose:  Initalise dom elements required for pagination with listbox input
         * Returns:  -
         * Inputs:   object:oSettings - dataTables settings object
         *           node:nPaging - the DIV which contains this pagination control
         *           function:fnCallbackDraw - draw function which must be called on update
         */
        fnInit: function (oSettings, nPaging, fnCallbackDraw) {
            var nInput = document.createElement('select');
            var nPage = document.createElement('span');
            var nOf = document.createElement('span');
            nOf.className = "paginate_of";
            nPage.className = "paginate_page";
            if (oSettings.sTableId !== '') {
                nPaging.setAttribute('id', oSettings.sTableId + '_paginate');
            }
            nInput.style.display = "inline";
            nPage.innerHTML = "{{Page}} ";
            nPaging.appendChild(nPage);
            nPaging.appendChild(nInput);
            nPaging.appendChild(nOf);
            this.nPaging = nPaging; /* Remember div for toggle */
            $(nInput).change(function (e) { /* Set DataTables page property and redraw the grid on listbox change event. */
                window.scroll(0,0); /* scroll to top of page */
                if (this.value === "" || this.value.match(/[^0-9]/)) { /* Nothing entered or non-numeric character */
                    return;
                }
                var iNewStart = oSettings._iDisplayLength * (this.value - 1);
                if (iNewStart > oSettings.fnRecordsDisplay()) { /* Display overrun */
                    oSettings._iDisplayStart = (Math.ceil((oSettings.fnRecordsDisplay() - 1) / oSettings._iDisplayLength) - 1) * oSettings._iDisplayLength;
                    fnCallbackDraw(oSettings);
                    return;
                }
                oSettings._iDisplayStart = iNewStart;
                fnCallbackDraw(oSettings);
            }); /* Take the brutal approach to cancelling text selection */
            $('span', nPaging).bind('mousedown', function () {
                return false;
            });
            $('span', nPaging).bind('selectstart', function () {
                return false;
            });
        },

        /*
         * Function: oPagination.listbox.fnUpdate
         * Purpose:  Update the listbox element
         * Returns:  -
         * Inputs:   object:oSettings - dataTables settings object
         *           function:fnCallbackDraw - draw function which must be called on update
         */
        fnUpdate: function (oSettings, fnCallbackDraw) {
            if (!oSettings.aanFeatures.p) {
                return;
            }
            var iPages = Math.ceil((oSettings.fnRecordsDisplay()) / oSettings._iDisplayLength);
            $(this.nPaging).toggle(!!iPages); /* Hide paging div for empty table */
            var iCurrentPage = Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength) + 1; /* Loop over each instance of the pager */
            var an = oSettings.aanFeatures.p;
            for (var i = 0, iLen = an.length; i < iLen; i++) {
                var spans = an[i].getElementsByTagName('span');
                var inputs = an[i].getElementsByTagName('select');
                var elSel = inputs[0];
                if(elSel.options.length != iPages) {
                    elSel.options.length = 0; /* clear the listbox contents */
                    for (var j = 0; j < iPages; j++) { /* add the pages */
                        var oOption = document.createElement('option');
                        oOption.text = j + 1;
                        oOption.value = j + 1;
                        try {
                            elSel.add(oOption, null); /* standards compliant; doesn't work in IE */
                        } catch (ex) {
                            elSel.add(oOption); /* IE only */
                        }
                    }
                    spans[1].innerHTML = '&nbsp; {{of}} &nbsp;' + iPages;
                }
                elSel.value = iCurrentPage;
            }
        }
    };

    $('#entities').DataTable({
        bSort: true,
        bJQueryUI: true,
        aLengthMenu: [ [25, 50, 100, -1], [25, 50, 100, '{{All}}'] ],
        iDisplayLength: 25,
        sPaginationType: 'listbox',
        bAutoWidth: false,
        oLanguage: { sUrl: '/resources/dataTables.'+language+'.json' },
        aoColumnDefs: [
            /* Adjust columns with icons */
            { bSortable: false, aTargets: [ 0, 5, 7 ] },
            { sWidth: "1%", aTargets: [ 0, 5, 7 ] }
        ],
        aaSorting: [[ 1, 'asc' ]],
        fnInitComplete: function() {
            /* Add clear search button */
            $('<img />')
            .prop('src', '/images/ico/cross-script.png')
            .prop('title', '{{ClearSearch}}')
            .css('margin-left', '4px')
            .css('cursor', 'pointer')
            .click(function() {
                 /* Clear search and force re-search */
                 $('#entities_filter').find('input').val('').trigger('keyup');
             })
            .appendTo($('#entities_filter'));
            $('select[name=list_length]').addClass('ui-corner-all');
        }
    });


    $("#dialog-confirm").dialog({
        autoOpen: false,
        resizable: false,
        width: 480,
        modal: true,
        buttons: {
            '{{Delete}}': function() { $(this).data('form').submit(); },
            '{{Cancel}}': function() { $(this).dialog('close'); }
        }
    });

    $('.delete-form').submit(function(){
            currentForm = this;
            $('#dialog-confirm').data('form', this).dialog('open');
            return false;
    });

    shortcut.add('Alt+N', function() { window.location = '/channel/add'; });

});

</script>
