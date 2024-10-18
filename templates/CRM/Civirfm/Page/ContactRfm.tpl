{crmScope extensionKey="civirfm"}
<div class="crm-content-block">
  <h4>{ts}RFM Fundraising{/ts}</h4>
    {if isset($model.date_calculated)}
    <table class="report-layout" style="max-width: 500px;">
      <thead>
        <tr>
          <th colspan="2">The following fundraising data was calculated on {ts 1=$model.date_calculated} %1{/ts}</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Last gift (recency)</td>
          <td>{ts 1=$model.recency} %1 days ago{/ts}</td>
        </tr>
        <tr>
          <td>Number of gifts (frequency)</td>
          <td>{ts 1=$model.frequency 2=$model.rfm_time} %1 in last %2 years{/ts}</td>
        </tr>
        <tr>
          <td>Average gift value (monetary)</td>
          <td>{ts 1=$model.curr_symbol 2=$model.monetary 3=$model.rfm_time} %1%2 in last %3 years{/ts}</td>
        </tr>
      </tbody>
    </table>
  {else}
    <p>No RFM data exists for this contact</p>
  {/if}
</div>
{/crmScope}
