<? use yii\easyii\modules\text\api\Text; ?>
<form class="row" method="get" action="/booking-engine">
                <input class="hidden" type="text" name="locale" value="<?=Yii::$app->language ?>" />
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl text-uppercase tt flex-end mb-20 mb-xl-0" style="padding-bottom: 5px;"><?=Text::get('tim-kiem');?></div>
                <div class="col-12 col-sm-12 col-md-4 col-lg mb-20 mb-sm-20 mb-md-0">
                    <label><?=Text::get('ngay-den');?></label>
                    <input name="check_in_date" type='text' class="date-picker date-go" class="form-control" />
                </div>
                <div class="col-12 col-sm-12 col-md-4 col-lg mb-20 mb-sm-20 mb-md-0">
                    <label><?=Text::get('ngay-di');?></label>
                    <input type="text"  class="date-picker date-come" name="check_out_date">
                </div>
                <div class="col-12 col-sm-12 col-md-4  col-lg no-gutters mb-20 mb-sm-20 mb-md-0">
                    <div class="row">
                        <div class="col col-sm col-lg text-lg-left text-left text-sm-center text-md-center pl-0">
                            <label><?=Text::get('ng-lon');?></label>
                            <select class="adult" name="number_adults">
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                                <option>6</option>
                                <option>7</option>
                                <option>8</option>
                                <option>9</option>
                                <option>10</option>
                            </select>
                        </div>
                        <div class="col col-sm col-lg float-lg-none float-right text-right text-lg-left pr-0">
                            <label class="text-left text-lg-left"><?=Text::get('tre-em');?></label>
                            <select class="children" name="number_children">
                                <option>0</option>
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                                <option>6</option>
                                <option>7</option>
                                <option>8</option>
                                <option>9</option>
                                <option>10</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-12  col-lg flex-end mb-20 mb-sm-20 mb-md-0 mt-20 mt-lg-0">
                    <button class="booking-body w-100 btn" type="submit"><?=Text::get('gui-yc');?></button>
                </div>
            </form>