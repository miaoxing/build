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

