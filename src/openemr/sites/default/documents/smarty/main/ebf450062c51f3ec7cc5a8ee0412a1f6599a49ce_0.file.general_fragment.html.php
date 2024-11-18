<?php
/* Smarty version 4.3.4, created on 2024-11-15 19:29:41
  from 'D:\EasyPHP-Devserver-17\eds-www\GVSU-CIS641-partyOfOne\src\openemr\templates\prescription\general_fragment.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.4',
  'unifunc' => 'content_673793159abbd7_70832610',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ebf450062c51f3ec7cc5a8ee0412a1f6599a49ce' => 
    array (
      0 => 'D:\\EasyPHP-Devserver-17\\eds-www\\GVSU-CIS641-partyOfOne\\src\\openemr\\templates\\prescription\\general_fragment.html',
      1 => 1700126886,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_673793159abbd7_70832610 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\EasyPHP-Devserver-17\\eds-www\\GVSU-CIS641-partyOfOne\\src\\openemr\\library\\smarty\\plugins\\function.xlt.php','function'=>'smarty_function_xlt',),));
if (empty($_smarty_tpl->tpl_vars['prescriptions']->value)) {
echo smarty_function_xlt(array('t'=>'None'),$_smarty_tpl);?>

<?php } else { ?>
<div class="table-responsive">
    <table class="table table-sm table-striped">
        <thead>
            <tr>
                <th><?php echo smarty_function_xlt(array('t'=>'Drug'),$_smarty_tpl);?>
</th>
                <th><?php echo smarty_function_xlt(array('t'=>'Details'),$_smarty_tpl);?>
</th>
                <th><?php echo smarty_function_xlt(array('t'=>'Qty'),$_smarty_tpl);?>
</th>
                <th><?php echo smarty_function_xlt(array('t'=>'Refills'),$_smarty_tpl);?>
</th>
                <th><?php echo smarty_function_xlt(array('t'=>'Filled'),$_smarty_tpl);?>
</th>
            </tr>
        </thead>
        <tbody>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['prescriptions']->value, 'prescription');
$_smarty_tpl->tpl_vars['prescription']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['prescription']->value) {
$_smarty_tpl->tpl_vars['prescription']->do_else = false;
?>
            <?php if ($_smarty_tpl->tpl_vars['prescription']->value->get_active() > 0) {?>
            <tr>
                <td><?php echo text($_smarty_tpl->tpl_vars['prescription']->value->drug);?>
&nbsp;</td>
                <td><?php echo text($_smarty_tpl->tpl_vars['prescription']->value->get_size());
echo text($_smarty_tpl->tpl_vars['prescription']->value->get_unit_display());?>
&nbsp;
                    <?php echo text($_smarty_tpl->tpl_vars['prescription']->value->get_dosage_display());?>
</td>
                <td><?php echo text($_smarty_tpl->tpl_vars['prescription']->value->get_quantity());?>
</td>
                <td><?php echo text($_smarty_tpl->tpl_vars['prescription']->value->get_refills());?>
</td>
                <td><?php echo text($_smarty_tpl->tpl_vars['prescription']->value->get_date_added());?>
</td>
            </tr>
            <?php }?>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </tbody>
    </table>
</div>
<?php }
}
}
