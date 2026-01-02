<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Venustheme
 * @package    Ves_Megamenu
 * @copyright  Copyright (c) 2019 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

namespace Ves\Megamenu\Api\Data;

interface DesignStyleInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const MARGIN_TOP = 'margin_top';
    const MARGIN_RIGHT = 'margin_right';
    const MARGIN_BOTTOM = 'margin_bottom';
    const MARGIN_LEFT = 'margin_left';
    const MARGIN_UNITS = 'margin_units';
    const BORDER_TOP_WIDTH = 'border_top_width';
    const BORDER_BOTTOM_WIDTH = 'border_bottom_width';
    const BORDER_LEFT_WIDTH = 'border_left_width';
    const BORDER_UNITS = 'border_units';
    const PADDING_TOP = 'padding_top';
    const PADDING_RIGHT = 'padding_right';
    const PADDING_BOTTOM = 'padding_bottom';
    const PADDING_LEFT = 'padding_left';
    const PADDING_UNITS = 'padding_units';
    const WIDTH = 'width';
    const BORDER_COLOR = 'border_color';
    const BORDER_STYLE = 'border_style';
    const BACKGROUND = 'background';
    const BOXSHADOW_UNITS = 'boxshadow_units';
    const BOXSHADOW_INSET = 'boxshadow_inset';
    const BOXSHADOW_X = 'boxshadow_x';
    const BOXSHADOW_Y = 'boxshadow_y';
    const BOXSHADOW_BLUR = 'boxshadow_blur';
    const BOXSHADOW_SPREAD = 'boxshadow_spread';
    const BOXSHADOW_COLOR = 'boxshadow_color';
    const FONT_SIZE = 'font_size';
    const FONT_GROUP = 'font_group';
    const FONT_CUSTOM = 'font_custom';
    const FONT_GOOGLE = 'font_google';
    const FONT_CHAR_SUBSET = 'font_char_subset';
    const FONT_WEIGHT = 'font_weight';
    const BORDER_TOP_LEFT_RADIUS = 'border_top_left_radius';
    const BORDER_TOP_RIGHT_RADIUS = 'border_top_right_radius';
    const BORDER_BOTTOM_RIGHT_RADIUS = 'border_bottom_right_radius';
    const BORDER_BOTTOM_LEFT_RADIUS = 'border_bottom_left_radius';
    const RADIUS_UNITS = 'radius_units';

    /**
     * Get margin_top
     * @return string|null
     */
    public function getMarginTop();

    /**
     * Set margin_top
     * @param string $margin_top
     * @return $this
     */
    public function setMarginTop($margin_top);

    /**
     * Get margin_right
     * @return string|null
     */
    public function getMarginRight();

    /**
     * Set margin_right
     * @param string $margin_right
     * @return $this
     */
    public function setMarginRight($margin_right);

    /**
     * Get margin_bottom
     * @return string|null
     */
    public function getMarginBottom();

    /**
     * Set margin_bottom
     * @param string $margin_bottom
     * @return $this
     */
    public function setMarginBottom($margin_bottom);

    /**
     * Get margin_left
     * @return string|null
     */
    public function getMarginLeft();

    /**
     * Set margin_left
     * @param string $margin_left
     * @return $this
     */
    public function setMarginLeft($margin_left);

    /**
     * Get margin_units
     * @return string|null
     */
    public function getMarginUnits();

    /**
     * Set margin_units
     * @param string $margin_units
     * @return $this
     */
    public function setMarginUnits($margin_units);

    /**
     * Get border_top_width
     * @return string|null
     */
    public function getBorderTopWidth();

    /**
     * Set border_top_width
     * @param string $border_top_width
     * @return $this
     */
    public function setBorderTopWidth($border_top_width);

    /**
     * Get border_bottom_width
     * @return string|null
     */
    public function getBorderBottomWidth();

    /**
     * Set border_bottom_width
     * @param string $border_bottom_width
     * @return $this
     */
    public function setBorderBottomWidth($border_bottom_width);

    /**
     * Get border_left_width
     * @return string|null
     */
    public function getBorderLeftWidth();

    /**
     * Set border_left_width
     * @param string $border_left_width
     * @return $this
     */
    public function setBorderLeftWidth($border_left_width);

    /**
     * Get border_units
     * @return string|null
     */
    public function getBorderUnits();

    /**
     * Set border_units
     * @param string $border_units
     * @return $this
     */
    public function setBorderUnits($border_units);

    /**
     * Get padding_top
     * @return string|null
     */
    public function getPaddingTop();

    /**
     * Set padding_top
     * @param string $padding_top
     * @return $this
     */
    public function setPaddingTop($padding_top);

    /**
     * Get padding_right
     * @return string|null
     */
    public function getPaddingRight();

    /**
     * Set padding_right
     * @param string $padding_right
     * @return $this
     */
    public function setPaddingRight($padding_right);

    /**
     * Get padding_bottom
     * @return string|null
     */
    public function getPaddingBottom();

    /**
     * Set padding_bottom
     * @param string $padding_bottom
     * @return $this
     */
    public function setPaddingBottom($padding_bottom);

    /**
     * Get padding_left
     * @return string|null
     */
    public function getPaddingLeft();

    /**
     * Set padding_left
     * @param string $padding_left
     * @return $this
     */
    public function setPaddingLeft($padding_left);

    /**
     * Get padding_units
     * @return string|null
     */
    public function getPaddingUnits();

    /**
     * Set padding_units
     * @param string $padding_units
     * @return $this
     */
    public function setPaddingUnits($padding_units);

    /**
     * Get width
     * @return string|null
     */
    public function getWidth();

    /**
     * Set width
     * @param string $width
     * @return $this
     */
    public function setWidth($width);

    /**
     * Get border_color
     * @return string|null
     */
    public function getBorderColor();

    /**
     * Set border_color
     * @param string $border_color
     * @return $this
     */
    public function setBorderColor($border_color);

    /**
     * Get border_style
     * @return string|null
     */
    public function getBorderStyle();

    /**
     * Set border_style
     * @param string $border_style
     * @return $this
     */
    public function setBorderStyle($border_style);

    /**
     * Get background
     * @return string|null
     */
    public function getBackground();

    /**
     * Set background
     * @param string $background
     * @return $this
     */
    public function setBackground($background);

    /**
     * Get boxshadow_units
     * @return string|null
     */
    public function getBoxshadowUnits();

    /**
     * Set boxshadow_units
     * @param string $boxshadow_units
     * @return $this
     */
    public function setBoxshadowUnits($boxshadow_units);

    /**
     * Get boxshadow_inset
     * @return string|null
     */
    public function getBoxshadowInset();

    /**
     * Set boxshadow_inset
     * @param string $boxshadow_inset
     * @return $this
     */
    public function setBoxshadowInset($boxshadow_inset);

    /**
     * Get boxshadow_x
     * @return string|null
     */
    public function getBoxshadowX();

    /**
     * Set boxshadow_x
     * @param string $boxshadow_x
     * @return $this
     */
    public function setBoxshadowX($boxshadow_x);

    /**
     * Get boxshadow_y
     * @return string|null
     */
    public function getBoxshadowY();

    /**
     * Set boxshadow_y
     * @param string $boxshadow_y
     * @return $this
     */
    public function setBoxshadowY($boxshadow_y);

    /**
     * Get boxshadow_blur
     * @return string|null
     */
    public function getBoxshadowBlur();

    /**
     * Set boxshadow_blur
     * @param string $boxshadow_blur
     * @return $this
     */
    public function setBoxshadowBlur($boxshadow_blur);

    /**
     * Get boxshadow_spread
     * @return string|null
     */
    public function getBoxshadowSpread();

    /**
     * Set boxshadow_spread
     * @param string $boxshadow_spread
     * @return $this
     */
    public function setBoxshadowSpread($boxshadow_spread);

    /**
     * Get boxshadow_color
     * @return string|null
     */
    public function getBoxshadowColor();

    /**
     * Set boxshadow_color
     * @param string $boxshadow_color
     * @return $this
     */
    public function setBoxshadowColor($boxshadow_color);

    /**
     * Get font_size
     * @return string|null
     */
    public function getFontSize();

    /**
     * Set font_size
     * @param string $font_size
     * @return $this
     */
    public function setFontSize($font_size);

    /**
     * Get font_group
     * @return string|null
     */
    public function getFontGroup();

    /**
     * Set font_group
     * @param string $font_group
     * @return $this
     */
    public function setFontGroup($font_group);

    /**
     * Get font_custom
     * @return string|null
     */
    public function getFontCustom();

    /**
     * Set font_custom
     * @param string $font_custom
     * @return $this
     */
    public function setFontCustom($font_custom);

    /**
     * Get font_google
     * @return string|null
     */
    public function getFontGoogle();

    /**
     * Set font_google
     * @param string $font_google
     * @return $this
     */
    public function setFontGoogle($font_google);

    /**
     * Get font_char_subset
     * @return string|null
     */
    public function getFontCharSubset();

    /**
     * Set font_char_subset
     * @param string $font_char_subset
     * @return $this
     */
    public function setFontCharSubset($font_char_subset);

    /**
     * Get font_weight
     * @return string|null
     */
    public function getFontWeight();

    /**
     * Set font_weight
     * @param string $font_weight
     * @return $this
     */
    public function setFontWeight($font_weight);

    /**
     * Get border_top_left_radius
     * @return string|null
     */
    public function getBorderTopLeftRadius();

    /**
     * Set border_top_left_radius
     * @param string $border_top_left_radius
     * @return $this
     */
    public function setBorderTopLeftRadius($border_top_left_radius);

    /**
     * Get border_top_right_radius
     * @return string|null
     */
    public function getBorderTopRightRadius();

    /**
     * Set border_top_right_radius
     * @param string $border_top_right_radius
     * @return $this
     */
    public function setBorderTopRightRadius($border_top_right_radius);

    /**
     * Get border_bottom_right_radius
     * @return string|null
     */
    public function getBorderBottomRightRadius();

    /**
     * Set border_bottom_right_radius
     * @param string $border_bottom_right_radius
     * @return $this
     */
    public function setBorderBottomRightRadius($border_bottom_right_radius);

    /**
     * Get border_bottom_left_radius
     * @return string|null
     */
    public function getBorderBottomLeftRadius();

    /**
     * Set border_bottom_left_radius
     * @param string $border_bottom_left_radius
     * @return $this
     */
    public function setBorderBottomLeftRadius($border_bottom_left_radius);

    /**
     * Get radius_units
     * @return string|null
     */
    public function getRadiusUnits();

    /**
     * Set radius_units
     * @param string $radius_units
     * @return $this
     */
    public function setRadiusUnits($radius_units);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Ves\Megamenu\Api\Data\DesignStyleExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Ves\Megamenu\Api\Data\DesignStyleExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Ves\Megamenu\Api\Data\DesignStyleExtensionInterface $extensionAttributes
    );
}
