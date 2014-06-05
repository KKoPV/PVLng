
<table class="dataTable">
<thead>
    <tr>
        <th>{{Id}}</th>
        <th>{{Name}}</th>
        <th>{{ExampleUnit}}</th>
        <th>{{Model}}</th>
        <th>{{Type}}</th>
        <th>{{Childs}}</th>
        <th></th>
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
                <span class="sort">X</span>
                {{unlimited}}
            <!-- ELSEIF {CHILDS} == 0 -->
                <span class="sort">0</span>
                {{no}}
            <!-- ELSE -->
                <span class="sort">{CHILDS}</span>
                {CHILDS}
            <!-- ENDIF -->
        </td>
        <td class="icons">
            <!-- Add invisible spans for sorting -->
            <!-- IF {READ} AND {WRITE} -->
                <span class="sort">1</span>
                <img src="/images/ico/read-write.png" alt="rw">
            <!-- ELSEIF {WRITE} -->
                <span class="sort">2</span>
                <img src="/images/ico/write.png" alt="w">
            <!-- ELSEIF {READ} -->
                <span class="sort">3</span>
                <img src="/images/ico/read.png" alt="r">
            <!-- ELSE -->
                <span class="sort">4</span>
                <img src="/images/pix.gif" alt="">
            <!-- ENDIF -->

            <!-- IF {GRAPH} -->
                <img src="/images/ico/chart.png" alt="g">
            <!-- ELSE -->
                <img src="/images/pix.gif" alt="">
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

</table>

<div id="legend" class="icons">
    <strong>{{Legend}}</strong>:
    <span><img src="/images/ico/read-write.png">{{ReadWritableEntity}}</span>,
    <span><img src="/images/ico/write.png">{{WritableEntity}}</span>,
    <span><img src="/images/ico/read.png">{{ReadableEntity}}</span>,
    <span><img src="/images/ico/chart.png">{{UsableInCharts}}</span>,
    <span><img src="/images/ico/document--plus.png">{{AddChannel}}</span>,
    <span><img src="/images/ico/plus_circle_frame.png">{{ShowDescription}}</span>
</div>
