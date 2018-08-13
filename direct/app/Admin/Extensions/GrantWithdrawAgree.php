<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class GrantWithdrawAgree {
    protected $id;
    protected $url;

    public function __construct($id, $url) {
        $this->id = $id;
        $this->url = $url;
    }

    protected function script() {
        return <<<SCRIPT
$('.grant-agree').off('click').on('click', function () {
    var id = $(this).data('id');
    var url = $(this).data('url');
    bootbox.confirm({
        message: "确定要通过吗?",
        buttons: {
        confirm: {
            label: '通过',
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
                '/admin/withdraw/confirmPayment',
                {
                    id: id
                },
                function(ret) {
                    if (ret == 'success') {
                        var url = '/admin/withdraw/withdrawList';
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
<a href="javascript:void(0);" data-id="{$this->id}"  class="grant-agree">
    <button class="btn btn-info" style="width:80px;height:25px;line-height: 12px; text-align:center; font-size: 12px;">通过申请</button>
</a>
EOT;
    }

    public function __toString() {
        return $this->render();
    }
}