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
package net.sf.jasperreports.components.barcode4j;

import net.sf.jasperreports.engine.DefaultJasperReportsContext;
import net.sf.jasperreports.engine.JRComponentElement;
import net.sf.jasperreports.engine.JRDefaultStyleProvider;
import net.sf.jasperreports.engine.JRStyle;
import net.sf.jasperreports.engine.JasperReportsContext;

import org.krysalis.barcode4j.BaselineAlignment;
import org.krysalis.barcode4j.ChecksumMode;
import org.krysalis.barcode4j.HumanReadablePlacement;
import org.krysalis.barcode4j.impl.AbstractBarcodeBean;
import org.krysalis.barcode4j.impl.codabar.CodabarBean;
import org.krysalis.barcode4j.impl.code128.Code128Bean;
import org.krysalis.barcode4j.impl.code128.EAN128Bean;
import org.krysalis.barcode4j.impl.code39.Code39Bean;
import org.krysalis.barcode4j.impl.datamatrix.DataMatrixBean;
import org.krysalis.barcode4j.impl.datamatrix.SymbolShapeHint;
import org.krysalis.barcode4j.impl.fourstate.AbstractFourStateBean;
import org.krysalis.barcode4j.impl.fourstate.RoyalMailCBCBean;
import org.krysalis.barcode4j.impl.fourstate.USPSIntelligentMailBean;
import org.krysalis.barcode4j.impl.int2of5.Interleaved2Of5Bean;
import org.krysalis.barcode4j.impl.pdf417.PDF417Bean;
import org.krysalis.barcode4j.impl.postnet.POSTNETBean;
import org.krysalis.barcode4j.impl.upcean.EAN13Bean;
import org.krysalis.barcode4j.impl.upcean.EAN8Bean;
import org.krysalis.barcode4j.impl.upcean.UPCABean;
import org.krysalis.barcode4j.impl.upcean.UPCEBean;
import org.krysalis.barcode4j.tools.UnitConv;

/**
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: AbstractBarcodeEvaluator.java 5050 2012-03-12 10:11:26Z teodord $
 */
public abstract class AbstractBarcodeEvaluator implements BarcodeVisitor
{
	
	protected final JasperReportsContext jasperReportsContext;
	protected final JRComponentElement componentElement;
	protected final BarcodeComponent barcodeComponent;
	protected final JRDefaultStyleProvider defaultStyleProvider;

	protected String message;
	protected AbstractBarcodeBean barcode;
	
	protected AbstractBarcodeEvaluator(
		JasperReportsContext jasperReportsContext,
		JRComponentElement componentElement, 
		JRDefaultStyleProvider defaultStyleProvider
		)
	{
		this.jasperReportsContext = jasperReportsContext;
		this.componentElement = componentElement;
		this.barcodeComponent = (BarcodeComponent) componentElement.getComponent();
		this.defaultStyleProvider = defaultStyleProvider;
	}

	/**
	 * @deprecated Replaced by {@link #AbstractBarcodeEvaluator(JasperReportsContext, JRComponentElement, JRDefaultStyleProvider)}.
	 */
	protected AbstractBarcodeEvaluator(JRComponentElement componentElement, 
			JRDefaultStyleProvider defaultStyleProvider)
	{
		this(DefaultJasperReportsContext.getInstance(), componentElement, defaultStyleProvider);
	}

	public void evaluateBarcode()
	{
		BarcodeComponent barcodeComponent = 
			(BarcodeComponent) componentElement.getComponent();
		barcodeComponent.receive(this);
	}
	
	public String getMessage()
	{
		return message;
	}
	
	public AbstractBarcodeBean getBarcode()
	{
		return barcode;
	}

	protected void setBaseAttributes(BarcodeComponent barcodeComponent)
	{
		JRStyle style = getElementStyle();
		if (style != null)
		{
			String fontName = style.getFontName();
			if (fontName != null)
			{
				barcode.setFontName(fontName);
			}
			
			Integer fontSize = style.getFontSize();
			if (fontSize != null)
			{
				double barFontSize = UnitConv.pt2mm(fontSize.intValue());
				barcode.setFontSize(barFontSize);
			}
		}
		
		Double moduleWidth = barcodeComponent.getModuleWidth();
		if (moduleWidth != null)
		{
			barcode.setModuleWidth(UnitConv.pt2mm(moduleWidth.doubleValue()));
		}
		
		String textPlacement = barcodeComponent.getTextPosition();
		if (textPlacement != null)
		{
			barcode.setMsgPosition(
					HumanReadablePlacement.byName(textPlacement));
		}
		
		Double quietZone = barcodeComponent.getQuietZone();
		if (quietZone != null)
		{
			barcode.doQuietZone(true);
			barcode.setQuietZone(UnitConv.pt2mm(quietZone.doubleValue()));
		}
		
		Double vQuietZone = barcodeComponent.getVerticalQuietZone();
		if (vQuietZone != null)
		{
			barcode.setVerticalQuietZone(UnitConv.pt2mm(vQuietZone.doubleValue()));
		}

		// FIXME DataMatrix?
		double barcodeHeight;
		if (BarcodeUtils.isVertical(barcodeComponent))
		{
			barcodeHeight = UnitConv.pt2mm(componentElement.getWidth());
		}
		else
		{
			barcodeHeight = UnitConv.pt2mm(componentElement.getHeight());
		}
		barcode.setHeight(barcodeHeight);
		
	}

	protected JRStyle getElementStyle()
	{
		JRStyle style = componentElement.getStyle();
		if (style == null)
		{
			style = defaultStyleProvider.getDefaultStyle();
		}
		return style;
	}
	
	public void visitCodabar(CodabarComponent codabar)
	{
		CodabarBean codabarBean = new CodabarBean();
		barcode = codabarBean;
		evaluateCodabar(codabar);
		setBaseAttributes(codabar);
		if (codabar.getWideFactor() != null)
		{
			codabarBean.setWideFactor(codabar.getWideFactor().doubleValue());
		}
	}

	protected abstract void evaluateCodabar(CodabarComponent codabar);

	public void visitCode128(Code128Component code128)
	{
		barcode = new Code128Bean();
		evaluateCode128(code128);
		setBaseAttributes(code128);
	}

	protected abstract void evaluateCode128(Code128Component code128);

	public void visitDataMatrix(DataMatrixComponent dataMatrix)
	{
		DataMatrixBean dataMatrixBean = new DataMatrixBean();
		barcode = dataMatrixBean;
		evaluateDataMatrix(dataMatrix);
		setBaseAttributes(dataMatrix);
		if (dataMatrix.getShape() != null)
		{
			dataMatrixBean.setShape(SymbolShapeHint.byName(dataMatrix.getShape()));
		}
	}

	protected abstract void evaluateDataMatrix(DataMatrixComponent dataMatrix);

	public void visitEANCode128(EAN128Component ean128)
	{
		EAN128Bean ean128Bean = new EAN128Bean();
		barcode = ean128Bean;
		evaluateEANCode128(ean128);
		setBaseAttributes(ean128);
		if (ean128.getChecksumMode() != null)
		{
			ean128Bean.setChecksumMode(ChecksumMode.byName(ean128.getChecksumMode()));
		}
	}

	protected abstract void evaluateEANCode128(EAN128Component ean128);

	public void visitCode39(Code39Component code39)
	{
		Code39Bean code39Bean = new Code39Bean();
		barcode = code39Bean;
		evaluateCode39(code39);
		setBaseAttributes(code39);
		if (code39.getChecksumMode() != null)
		{
			code39Bean.setChecksumMode(ChecksumMode.byName(code39.getChecksumMode()));
		}
		if (code39.isDisplayChecksum() != null)
		{
			code39Bean.setDisplayChecksum(code39.isDisplayChecksum().booleanValue());
		}
		if (code39.isDisplayStartStop() != null)
		{
			code39Bean.setDisplayStartStop(code39.isDisplayStartStop().booleanValue());
		}
		if (code39.isExtendedCharSetEnabled() != null)
		{
			code39Bean.setExtendedCharSetEnabled(code39.isExtendedCharSetEnabled().booleanValue());
		}
		if (code39.getIntercharGapWidth() != null)
		{
			code39Bean.setIntercharGapWidth(code39.getIntercharGapWidth().doubleValue());
		}
		if (code39.getWideFactor() != null)
		{
			code39Bean.setWideFactor(code39.getWideFactor().doubleValue());
		}
	}

	protected abstract void evaluateCode39(Code39Component code39);

	protected abstract void evaluateInterleaved2Of5(Interleaved2Of5Component interleaved2Of5);

	public void visitInterleaved2Of5(Interleaved2Of5Component interleaved2Of5)
	{
		Interleaved2Of5Bean interleaved2Of5Bean = new Interleaved2Of5Bean();
		barcode = interleaved2Of5Bean;
		evaluateInterleaved2Of5(interleaved2Of5);
		setBaseAttributes(interleaved2Of5);
		if (interleaved2Of5.getChecksumMode() != null)
		{
			interleaved2Of5Bean.setChecksumMode(ChecksumMode.byName(interleaved2Of5.getChecksumMode()));
		}
		if (interleaved2Of5.isDisplayChecksum() != null)
		{
			interleaved2Of5Bean.setDisplayChecksum(interleaved2Of5.isDisplayChecksum().booleanValue());
		}
		if (interleaved2Of5.getWideFactor() != null)
		{
			interleaved2Of5Bean.setWideFactor(interleaved2Of5.getWideFactor().doubleValue());
		}
	}

	public void visitUPCA(UPCAComponent upcA)
	{
		UPCABean upcABean = new UPCABean();
		barcode = upcABean;
		evaluateUPCA(upcA);
		setBaseAttributes(upcA);
		if (upcA.getChecksumMode() != null)
		{
			upcABean.setChecksumMode(ChecksumMode.byName(upcA.getChecksumMode()));
		}
	}

	protected abstract void evaluateUPCA(UPCAComponent upcA);

	public void visitUPCE(UPCEComponent upcE)
	{
		UPCEBean upcEBean = new UPCEBean();
		barcode = upcEBean;
		evaluateUPCE(upcE);
		setBaseAttributes(upcE);
		if (upcE.getChecksumMode() != null)
		{
			upcEBean.setChecksumMode(ChecksumMode.byName(upcE.getChecksumMode()));
		}
	}

	protected abstract void evaluateUPCE(UPCEComponent upcE);

	public void visitEAN13(EAN13Component ean13)
	{
		EAN13Bean ean13Bean = new EAN13Bean();
		barcode = ean13Bean;
		evaluateEAN13(ean13);
		setBaseAttributes(ean13);
		if (ean13.getChecksumMode() != null)
		{
			ean13Bean.setChecksumMode(ChecksumMode.byName(ean13.getChecksumMode()));
		}
	}

	protected abstract void evaluateEAN13(EAN13Component ean13);

	public void visitEAN8(EAN8Component ean8)
	{
		EAN8Bean ean8Bean = new EAN8Bean();
		barcode = ean8Bean;
		evaluateEAN8(ean8);
		setBaseAttributes(ean8);
		if (ean8.getChecksumMode() != null)
		{
			ean8Bean.setChecksumMode(ChecksumMode.byName(ean8.getChecksumMode()));
		}
	}

	protected abstract void evaluateEAN8(EAN8Component ean8);

	public void visitRoyalMailCustomer(
			RoyalMailCustomerComponent royalMailCustomer)
	{
		RoyalMailCBCBean mailBean = new RoyalMailCBCBean();
		barcode = mailBean;
		evaluateRoyalMailCustomer(royalMailCustomer);
		setBaseAttributes(royalMailCustomer);
		setFourStateAttributes(royalMailCustomer, mailBean);
	}

	protected void setFourStateAttributes(
			FourStateBarcodeComponent barcodeComponent, 
			AbstractFourStateBean barcodeBean)
	{
		if (barcodeComponent.getChecksumMode() != null)
		{
			barcodeBean.setChecksumMode(
					ChecksumMode.byName(barcodeComponent.getChecksumMode()));
		}
		
		if (barcodeComponent.getAscenderHeight() != null)
		{
			barcodeBean.setAscenderHeight(
					UnitConv.pt2mm(barcodeComponent.getAscenderHeight().doubleValue()));
		}
		
		if (barcodeComponent.getIntercharGapWidth() != null)
		{
			barcodeBean.setIntercharGapWidth(
					UnitConv.pt2mm(barcodeComponent.getIntercharGapWidth().doubleValue()));
		}
		
		if (barcodeComponent.getTrackHeight() != null)
		{
			barcodeBean.setTrackHeight(
					UnitConv.pt2mm(barcodeComponent.getTrackHeight().doubleValue()));
		}
	}

	protected abstract void evaluateRoyalMailCustomer(
			RoyalMailCustomerComponent royalMailCustomer);

	public void visitUSPSIntelligentMail(
			USPSIntelligentMailComponent intelligentMail)
	{
		USPSIntelligentMailBean mailBean = new USPSIntelligentMailBean();
		barcode = mailBean;
		evaluateUSPSIntelligentMail(intelligentMail);
		setBaseAttributes(intelligentMail);
		setFourStateAttributes(intelligentMail, mailBean);
	}

	protected abstract void evaluateUSPSIntelligentMail(
			USPSIntelligentMailComponent intelligentMail);

	public void visitPostnet(POSTNETComponent postnet)
	{
		POSTNETBean postnetBean = new POSTNETBean();
		barcode = postnetBean;
		evaluatePOSTNET(postnet);
		setBaseAttributes(postnet);
		
		if (postnet.getShortBarHeight() != null)
		{
			postnetBean.setShortBarHeight(
					UnitConv.pt2mm(postnet.getShortBarHeight().doubleValue()));
		}
		
		if (postnet.getBaselinePosition() != null)
		{
			postnetBean.setBaselinePosition(
					BaselineAlignment.byName(postnet.getBaselinePosition()));
		}
		
		if (postnet.getChecksumMode() != null)
		{
			postnetBean.setChecksumMode(
					ChecksumMode.byName(postnet.getChecksumMode()));
		}
		
		if (postnet.getDisplayChecksum() != null)
		{
			postnetBean.setDisplayChecksum(
					postnet.getDisplayChecksum().booleanValue());
		}
		
		if (postnet.getIntercharGapWidth() != null)
		{
			postnetBean.setIntercharGapWidth(
					UnitConv.pt2mm(postnet.getIntercharGapWidth().doubleValue()));
		}
	}

	protected abstract void evaluatePOSTNET(
			POSTNETComponent intelligentMail);

	public void visitPDF417(PDF417Component pdf417)
	{
		PDF417Bean pdf417Bean = new PDF417Bean();
		barcode = pdf417Bean;
		evaluatePDF417(pdf417);
		setBaseAttributes(pdf417);
		
		if (pdf417.getMinColumns() != null)
		{
			pdf417Bean.setMinCols(pdf417.getMinColumns().intValue());
		}
		if (pdf417.getMaxColumns() != null)
		{
			pdf417Bean.setMaxCols(pdf417.getMaxColumns().intValue());
		}
		if (pdf417.getMinRows() != null)
		{
			pdf417Bean.setMinRows(pdf417.getMinRows().intValue());
		}
		if (pdf417.getMaxRows() != null)
		{
			pdf417Bean.setMaxRows(pdf417.getMaxRows().intValue());
		}
		if (pdf417.getWidthToHeightRatio() != null)
		{
			pdf417Bean.setWidthToHeightRatio(
					pdf417.getWidthToHeightRatio().doubleValue());
		}
		if (pdf417.getErrorCorrectionLevel() != null)
		{
			pdf417Bean.setErrorCorrectionLevel(
					pdf417.getErrorCorrectionLevel().intValue());
		}
	}

	protected abstract void evaluatePDF417(PDF417Component pdf417);

}
