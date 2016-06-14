<div id="content" class="container">
	<div class="page-header">
		<h1> <?php echo $page_title; ?><small></small></h1>
	</div>
	<div class="row">
		<div class="well col-lg-12">
			<a class="btn btn-primary" href="./<?=$current_plugin?>/create_new_row"><?=$create_new_row?></a>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<?php foreach($header as $i => $th):?>
						<?php if($i > 0):?>
						<th><?=$th?></th>
						<?php endif; endforeach?>
					</tr>
				</thead>
				<?php 
				if(!empty($pages)):
				foreach($pages as $page):?>
				<thead>
				<tr><th colspan="<?=(count($body) - 2)?>"><?=$page['PAGE']?></th></tr></thead>
				<tbody>
				<?php foreach($body[4] as $id => $tr):?>
				<?php if($tr === $page['ID']):?>
				<tr>
					<?php 
					$rowid = $body[0][$id]; //Save the id in a variable
					$e = 0; //Set each row a unique ID
					foreach($body as $i => $val): //Display all the values 
					if($i > 0 && $i != 4): $e++; //Only display the values of the set page, if the value is display sum 1 to the unique ID
					$tdval = ($e == 1)?'<a href="./'.$current_plugin.'/update_table_row/'.$rowid.'">'.$val[$id].'</a>':$val[$id]; //Add an anchor to the first value
					$tdval = ($e == 2)?strip_tags(word_limiter($tdval, 6)):$tdval;
					?>
					<td><?=$tdval?></td>
					<?php endif; endforeach?>
				</tr>
				<?php endif; endforeach?>
				<?php endforeach?>
				</tbody>
				<?php endif?>
			</table>
		</div>
	</div>
</div>