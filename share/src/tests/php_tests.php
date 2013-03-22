<table style="border: solid 1px black; width:100%;">
<tr style="text-align:left;"><th>TEST</th><th>Not Set</th><th>NULL</th><th>Zero</th><th>FALSE</th><th>Numeric Value</th><th>Empty String</th></tr>
<tfoot><tr><td colspan="6">Comparison Table</td></tr></tfoot>
<tbody>
<?php
    /*** turn on error reporting ***/

    /*** an array of test values ***/
    $values = array( $var, null, 0, false, 100, '');
    echo '<tr>';
    echo '<td>isset()</td>';
    foreach( $values as $val )
    {
        echo '<td>';
        var_dump( isset( $val ) );
        echo '</td>';
    }
    echo '</tr>';

        echo '<tr>';
    echo '<td>empty()</td>';
        foreach( $values as $val )
        {
                echo '<td>';
                var_dump( empty( $val ) );
                echo '</td>';
        }
    echo '</tr>';

        echo '<tr>';
        echo '<td>is_null()</td>';
        foreach( $values as $val )
        {
                echo '<td>';
                var_dump( is_null( $val ) );
                echo '</td>';
        }
        echo '</tr>';

        echo '<tr>';
    echo '<td>== false</td>';
        foreach( $values as $val )
        {
                echo '<td>';
                var_dump( $val == false  );
                echo '</td>';
        }
    echo '</tr>';

        echo '<tr>';
    echo '<td>=== false</td>';
        foreach( $values as $val )
        {
                echo '<td>';
                var_dump( $val === false );
                echo '</td>';
        }
        echo '</tr>';
    echo '<td>== null</td>';
        foreach( $values as $val )
        {
                echo '<td>';
                var_dump( $val == false  );
                echo '</td>';
        }
    echo '</tr>';

        echo '<tr>';
    echo '<td>=== null</td>';
        foreach( $values as $val )
        {
                echo '<td>';
                var_dump( $val === false );
                echo '</td>';
        }
        echo '</tr>';
?>
</tbody>
</table>