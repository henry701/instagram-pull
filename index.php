<section id='instagram'>
	<?php
		require_once './API-Insta.php';
		$instagram = Get_Fotos_Recentes('instagram');
	?>
	<?php foreach (array_slice($instagram, 0, 6) as $img_rec) : ?>
		<a target="_blank" href="<?php echo eschtmlc($img_rec['link']); ?>">
			<img src='<?php echo eschtmlc($img_rec['images']['lowres']['url']); ?>' width='<?php echo eschtmlc($img_rec['images']['lowres']['width']); ?>' height='<?php echo eschtmlc($img_rec['images']['lowres']['height']); ?>' />
			<span><span><?php echo eschtmlc($img_rec['texto']); ?></span></span>
		</a>	
	<?php endforeach; ?>
</section>
