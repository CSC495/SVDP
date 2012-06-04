/*
 * JasperReports - Free Java Reporting Library.
 * Copyright (C) 2001 - 2011 Jaspersoft Corporation. All rights reserved.
 * http://www.jaspersoft.com
 *
 * Unless you have purchased a commercial license agreement from Jaspersoft,
 * the following license terms apply:
 *
 * This program is part of JasperReports.
 *
 * JasperReports is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * JasperReports is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with JasperReports. If not, see <http://www.gnu.org/licenses/>.
 */
package net.sf.jasperreports.components.barbecue;

import java.util.HashMap;
import java.util.Map;

import net.sf.jasperreports.engine.JRComponentElement;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRPrintElement;
import net.sf.jasperreports.engine.JRStyle;
import net.sf.jasperreports.engine.component.BaseFillComponent;
import net.sf.jasperreports.engine.component.FillPrepareResult;
import net.sf.jasperreports.engine.fill.JRTemplateImage;
import net.sf.jasperreports.engine.fill.JRTemplatePrintImage;
import net.sf.jasperreports.engine.type.EvaluationTimeEnum;
import net.sf.jasperreports.engine.type.ScaleImageEnum;
import net.sf.jasperreports.engine.util.JRStringUtil;
import net.sourceforge.barbecue.Barcode;

/**
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: BarbecueFillComponent.java 5050 2012-03-12 10:11:26Z teodord $
 */
public class BarbecueFillComponent extends BaseFillComponent
{

	private final BarbecueComponent barcodeComponent;
	
	private final Map<JRStyle, JRTemplateImage> printTemplates = new HashMap<JRStyle, JRTemplateImage>();
	
	private String code;
	private String applicationIdentifier;
	
	public BarbecueFillComponent(BarbecueComponent barcode)
	{
		this.barcodeComponent = barcode;
	}
	
	protected BarbecueComponent getBarcode()
	{
		return barcodeComponent;
	}
	
	public void evaluate(byte evaluation) throws JRException
	{
		if (isEvaluateNow())
		{
			evaluateBarcode(evaluation);
		}
	}
	
	protected void evaluateBarcode(byte evaluation) throws JRException
	{
		code = JRStringUtil.getString(fillContext.evaluate(barcodeComponent.getCodeExpression(), evaluation));
		
		applicationIdentifier = JRStringUtil.getString(fillContext.evaluate(barcodeComponent.getApplicationIdentifierExpression(), evaluation));
	}
	
	protected boolean isEvaluateNow()
	{
		return barcodeComponent.getEvaluationTimeValue() == EvaluationTimeEnum.NOW;
	}

	public FillPrepareResult prepare(int availableHeight)
	{
		//FIXMENOW do like for map and spider chart, because it crashes with null code one evaluationTime != NOW; check barbecue too
		return isEvaluateNow() && code == null 
				? FillPrepareResult.NO_PRINT_NO_OVERFLOW
				: FillPrepareResult.PRINT_NO_STRETCH;
	}

	public JRPrintElement fill()
	{
		JRTemplateImage templateImage = getTemplateImage();
		
		JRTemplatePrintImage image = new JRTemplatePrintImage(templateImage, elementId);
		JRComponentElement element = fillContext.getComponentElement();
		image.setX(element.getX());
		image.setY(fillContext.getElementPrintY());
		image.setWidth(element.getWidth());
		image.setHeight(element.getHeight());
		
		if (isEvaluateNow())
		{
			setBarcodeImage(image);
		}
		else
		{
			fillContext.registerDelayedEvaluation(image, 
					barcodeComponent.getEvaluationTimeValue(), 
					barcodeComponent.getEvaluationGroup());
		}
		
		return image;
	}

	public void evaluateDelayedElement(JRPrintElement element, byte evaluation)
			throws JRException
	{
		evaluateBarcode(evaluation);
		setBarcodeImage((JRTemplatePrintImage) element);
	}

	protected void setBarcodeImage(JRTemplatePrintImage image)
	{
		BarcodeInfo barcodeInfo = new BarcodeInfo();
		barcodeInfo.setType(barcodeComponent.getType());
		barcodeInfo.setCode(code);
		barcodeInfo.setApplicationIdentifier(applicationIdentifier);
		barcodeInfo.setDrawText(barcodeComponent.isDrawText());
		barcodeInfo.setRequiresChecksum(barcodeComponent.isChecksumRequired());
		barcodeInfo.setBarWidth(barcodeComponent.getBarWidth());
		barcodeInfo.setBarHeight(barcodeComponent.getBarHeight());
		
		Barcode barcode = BarcodeProviders.createBarcode(barcodeInfo);
		BarbecueRenderer renderer = new BarbecueRenderer(barcode);
		renderer.setRotation(BarbecueStyleResolver.getRotationValue(fillContext.getComponentElement()));
		
		image.setRenderable(renderer);
	}

	protected JRTemplateImage getTemplateImage()
	{
		JRStyle elementStyle = fillContext.getElementStyle();
		JRTemplateImage templateImage = printTemplates.get(elementStyle);
		if (templateImage == null)
		{
			templateImage = new JRTemplateImage(
					fillContext.getElementOrigin(), 
					fillContext.getDefaultStyleProvider());
			templateImage.setStyle(elementStyle);
			templateImage.setScaleImage(ScaleImageEnum.RETAIN_SHAPE);
			
			templateImage = deduplicate(templateImage);
			printTemplates.put(elementStyle, templateImage);
		}
		return templateImage;
	}

}
