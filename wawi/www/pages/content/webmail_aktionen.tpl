<div style="text-align:center;">
  <table width="100%">
    <tr>
      <td align="left">
        <input type="button" name="invert" value="Invertiere Auswahl" />
      </td>
      <td align="center">
        Aktion: 
        <select name="do">
          <option value="read">Als gelesen markieren</option>
          <option value="spam">Als SPAM markieren</option>
        </select>
        <input type="submit" name="s" value="OK" />
      </td>
      <td align="right">
        <input type="button" name="prev" onclick="window.location.href='index.php?module=webmail&action=list&start=[PREV]'" value="<<" />
        &nbsp;
        Seite 
        <select name="page" onchange="window.location.href='index.php?module=webmail&action=list&start='+this.value*15">[PAGESELECT]</select> 
        von [NUMPAGES]
        &nbsp;
        <input type="button" name="next" onclick="window.location.href='index.php?module=webmail&action=list&start=[NEXT]'" value=">>" />
      </td>
    </tr>
  </table>
</div>
