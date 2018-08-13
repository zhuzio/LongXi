<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class GrantRefuse {
	protected $id;
	protected $url;

	public function __construct($id, $url) {
		$this->id = $id;
		$this->url = $url;
	}

	protected function script() {
		return <<<SCRIPT
$('.grant-refuse').off('click').on('click', function () {
    var id = $(this).data('id');
    var url = $(this).data('url');
    bootbox.confirm({
        message: "确定要驳回吗?",
        buttons: {
        confirm: {
            label: '驳回',
            className: 'btn-success'
        },
        cancel: {
            label: '取消',
            className: 'btn-danger'
        }
    },
        callback: function(result) {
            if (!result) {
                return;
            }
            $.post(
                '/admin/reportCenter/refuse',
                {
                    id: id
                },
                function(ret) {
                    if (ret == 'success') {
                        var url = '/admin/reportCenter/refuseList';
                        window.location.href = url;
                    }
                }
            )
        }
    });

});

SCRIPT;
	}

	protected function render() {
		Admin::script($this->script());

		return <<<EOT
<a href="javascript:void(0);" data-id="{$this->id}"  class="grant-refuse">
    <button class="btn btn-danger" style="width:80px;height:25px;line-height: 12px; text-align:center; font-size: 12px;">驳回申请</button>
</a>
EOT;
	}

	public function __toString() {
		return $this->render();
	}
}