<!-- Add invisible spans for sorting -->
<!-- IF {READ} AND {WRITE} -->
    <span style="display:none">1</span>
    <img src="/images/ico/read-write.png" alt="rw">
<!-- ELSEIF {WRITE} -->
    <span style="display:none">2</span>
    <img src="/images/ico/write.png" alt="w">
<!-- ELSEIF {READ} -->
    <span style="display:none">3</span>
    <img src="/images/ico/read.png" alt="r">
<!-- ELSE -->
    <span style="display:none">4</span>
<!-- ENDIF -->
