<h3>RFM fundraising information</h3>

{if isset($date_calc)}
  {* We have RFM values to display *}
  <p>The following fundraising data was calculated on {ts 1=$date_calc} %1{/ts}.</p>

  <p>Last gift (recency): {ts 1=$recency} %1 days ago{/ts}.<br />
     Number of gifts (frequency): {ts 1=$frequency 2=$rfm_time} %1 in last %2 years{/ts}.<br />
     Average gift value (monetary): {ts 1=$monetary 2=$rfm_time} %1 in last %2 years{/ts}.
  </p>
{else}
  {* No RFM values have been calculated *}
  <p>No RFM data exists for this contact</p>
{/if}