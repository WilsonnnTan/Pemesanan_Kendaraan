<ul>
  <?php foreach($pending as $b): ?>
    <li>
      <?= $b['id'] ?> - <?= $b['driver'] ?> - <form action="<?= site_url('approvals/'.$b['id']) ?>" method="post" style="display:inline;"><button>Approve</button></form>
    </li>
  <?php endforeach; ?>
</ul>