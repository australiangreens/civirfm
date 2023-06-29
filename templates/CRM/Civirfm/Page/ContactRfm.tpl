{crmScope extensionKey='civirfm'}
<div class='crm-content-block'>
  <h3>{ts}RFM fundraising information{/ts}</h3>

  {if isset($date_calc)}
    {* We have RFM values to display *}
    <table class="report-layout" style="max-width: 500px;">
      <thead>
        <tr>
          <th colspan="2">The following fundraising data was calculated on {ts 1=$date_calc} %1{/ts}</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Last gift (recency)</td>
          <td>{ts 1=$recency} %1 days ago{/ts}</td>
        </tr>
        <tr>
          <td>Number of gifts (frequency)</td>
          <td>{ts 1=$frequency 2=$rfm_time} %1 in last %2 years{/ts}</td>
        </tr>
        <tr>
          <td>Average gift value (monetary)</td>
          <td>{ts 1=$curr_symbol 2=$monetary 3=$rfm_time} %1%2 in last %3 years{/ts}</td>
        </tr>
      </tbody>
    </table>
  {else}
    {* No RFM values have been calculated *}
    <p>No RFM data exists for this contact</p>
  {/if}
</div>
{/crmScope}