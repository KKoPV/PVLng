
<table class="dataTable">
<thead>
    <tr>
        <th>{{Id}}</th>
        <th>{{Name}}</th>
        <th>{{ExampleUnit}}</th>
        <th>{{Model}}</th>
        <th>{{Type}}</th>
        <th><i class="ico node-select-all tip" title="{{AcceptChildCount}}"></i></th>
        <th><i class="ico information-frame tip" tip="#IconLegend"></i></th>
        <th></th>
    </tr>
</thead>

<tbody>
    <!-- BEGIN TYPES -->
    <tr data-id="{ID}">
        <td>{ID}</td>
        <td class="icons">
            <img src="{ICON}" class="channel-icon tip" title="{ICON}">
            <strong>{NAME}</strong>
        </td>
        <td>{UNIT}</td>
        <td>{MODEL}</td>
        <td>{TYPE}</td>
        <td class="c">
            <!-- Add invisible spans for sorting -->
            <!-- IF {CHILDS} == -1 -->
                <span class="sort">X</span>&infin;
            <!-- ELSEIF {CHILDS} == 0 -->-<!-- ELSE -->{CHILDS}<!-- ENDIF -->
        </td>
        <td class="icons">
            <!-- Add invisible spans for sorting -->
            <!-- IF {READ} AND {WRITE} -->
                <i class="ico drive-globe"></i>
            <!-- ELSEIF {WRITE} -->
                <i class="ico drive--pencil"></i>
            <!-- ELSEIF {READ} -->
                <i class="ico drive--arrow"></i>
            <!-- ELSE -->
                <i class="ico pix"></i>
            <!-- ENDIF -->

            <!-- IF {GRAPH} -->
                <i class="ico chart"></i>
            <!-- ELSE -->
                <i class="ico pix"></i>
            <!-- ENDIF -->

            <form action="channel/add" method="post">
            <input type="hidden" name="type" value="{ID}">
            <input type="image" src="/images/ico/document--plus.png"
                   style="background-color:transparent" alt="+">
            </form>
        </td>
        <td>
            <span style="display:none">{DESCRIPTION}</span>
        </td>
    </tr>
    <!-- END -->
</tbody>

<tfoot>
    <tr>
        <th colspan="6"></th>
        <th><i class="ico information-frame tip" tip="#IconLegend"></i></th>
        <th></th>
    </tr>
</tfoot>

</table>

<!-- Legend -->

<div id="IconLegend">
    <div class="icons legendtip">
        <i class="ico drive-globe"></i>{{ReadWritableEntity}}<br />
        <i class="ico drive--pencil"></i>{{WritableEntity}}<br />
        <i class="ico drive--arrow"></i>{{ReadableEntity}}<br />
        <i class="ico chart"></i>{{UsableInCharts}}<br />
        <i class="ico document--plus"></i>{{CreateChannel}}<br />
        <i class="ico plus-circle-frame"></i>{{ShowDescription}}
    </div>
</div>
