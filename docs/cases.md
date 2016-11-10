# 案例

问题1: `index.php: line 96, col 76, value must match the format: dash`

原因: `shopId`是camel形式

```html
<select class="js-cascading-item province form-control" id="shopId" name="shopId">
```

解决: `shopId`改为dash形式的`shop-id`

```html
<select class="js-cascading-item province form-control" id="shop-id" name="shopId">
```

---

问题2: `index.php: line 103, col 15, an element with the id "user-name" does not exist (should match `for` attribute)`

原因: label标签for属性指定了ID,但是页面中没有ID为user-name的标签

```html
<label for="user-name" class="control-label">
  姓名
</label>
<div class="col-control">
  <input type="text" name="userName" class="form-control">
</div>
```

解决: 为label对应的input加上`id="user-name"`

```html
<label for="user-name" class="control-label">
  姓名
</label>
<div class="col-control">
  <input type="text" id="user-name" name="userName" class="form-control">
</div>
```

---

问题3: `The method indexAction() has a Cyclomatic Complexity of 17. The configured cyclomatic complexity threshold is 15.`

原因: 方法的条件复杂度过高,一般是存在过多的`if`, `while`, `for`和`case`, `?`

解决: 拆分为多个小方法,如较独立的功能拆分为getXxx,queryXxx,handleXxx

参考:

1. https://phpmd.org/rules/codesize.html#cyclomaticcomplexity

---

问题4: `The method indexAction() has 139 lines of code. Current threshold is set to 100. Avoid really long methods.`

原因: 方法代码行数过多

解决: 拆分为多个小方法,如较独立的功能拆分为getXxx,queryXxx,handleXxx

---

问题5: `Assignments must be the first block of code on a line (Squiz.PHP.DisallowMultipleAssignments.Found)`

原因: 变量赋值不是在一行代码的第一部分

```php
$this->user || $this->user = wei()->user()->findOrInitById($this['userId']);
```

解决: 改为完整的条件语句,使逻辑更清晰

```php
if (!$this->user) {
    $this->user = wei()->user()->findOrInitById($this['userId']);
}
```

----

问题6: htmllint检查JS模板文件时,如xxx/adminOrdersShowItem.php,会出现大量的无意义提示

如

```
xxx/adminOrdersShowItem.php: line 2, col 24, the `foundGroupBuying` attribute is not double quoted
xxx/adminOrdersShowItem.php: line 2, col 4, attribute names must match the format: dash
xxx/adminOrdersShowItem.php: line 2, col 30, attribute values cannot be empty
xxx/adminOrdersShowItem.php: line 10, col 54, the `!` attribute is not double quoted
```

原因: 模板文件中的`<%`导致html标签解析错误

解决: 先在模板文件头部增加配置,跳过检查.待以后升级模板引擎或配置,以支持检查

```
<!-- htmllint preset="none" -->
<!-- htmllint tag-name-match="false" -->
```
