<?php
/* @var $this yii\web\View */
use app\models\Message;
use kartik\file\FileInput;
use yii\helpers\Url;
use app\models;

$this->title = 'Messenger';
?>
<div class="site-index">
	<div class="messages">
		<h1>Messages</h1>
		<?php foreach (Message::find()->all() as $message): ?>
			<div class="message" id="<?php echo $message->id ?>">
				<h4><?php echo $message->user_name ?> says:</h4>
				<?php echo $message->content ?>
				<br>
				<br>
				<small><?php echo date("d M H:i", strtotime($message->created_at)) ?></small>
				<a class="like" href="#">Like <span><?= count($message->likes) > 0 ? count($message->likes) : '' ?></span></a>
				<br><a href="#" id="delete">Удалить</a>
			</div>
		<?php endforeach; ?>
	</div>

	<div class="create-message">
		<div class="create-message-wrapper">
			<h3>New Message:</h3>
			<textarea id="message-text"></textarea>
			<br>
			<div id="attaches"></div>
		</div>
		<button id="send-message" class="btn btn-primary pull-left">Write Message</button>

		<div class="dropdown pull-right">
			<button class="btn btn-default dropdown-toggle" type="button" id="attach" data-toggle="dropdown" aria-expanded="true">
				Attach
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu" role="menu" aria-labelledby="attach">
				<li role="presentation"><a role="menuitem" tabindex="-1" href="#" class="add-pictures-link">Picture</a></li>
				<li role="presentation" class="divider"></li>
				<li role="presentation"><a role="menuitem" tabindex="-1" href="#" class="add-video-link">Youtube Video</a></li>
			</ul>
		</div>

		<div class="dialog add-pictures-dialog" id="upload-picture">
			<h2>Upload pictures</h2>
			<?php
				echo FileInput::widget([
					'name' => 'UploadForm[file]',
					'pluginOptions' => [
						'showCaption'  => false,
						'showRemove'   => false,
						'showUpload'   => false,
						'uploadUrl'    => Url::to(['/messenger/upload']),
						'maxFileCount' => 10
					],
					'options'      => [
						'accept'   => 'image/*',
						'multiple' => true
					]
				]);
			?>
			<br>
			<a href="#" class="close-dialog pull-right btn btn-default">Close</a>
		</div>

		<div class="dialog add-video-dialog" id="add-video">
			<div class="form-group">
				<label for="video-url">Ссылка на видео</label>
				<input type="email" class="form-control" id="video-url" placeholder="">
			</div>
			<a href="#" class="add-video pull-left btn btn-xs btn-primary">Add Video</a>
			<a href="#" class="close-dialog pull-right btn btn-xs  btn-default">Close</a>
		</div>

	</div>
</div>