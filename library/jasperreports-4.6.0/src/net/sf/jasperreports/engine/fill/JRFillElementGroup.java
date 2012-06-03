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
package net.sf.jasperreports.engine.fill;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

import net.sf.jasperreports.engine.JRChild;
import net.sf.jasperreports.engine.JRElement;
import net.sf.jasperreports.engine.JRElementGroup;
import net.sf.jasperreports.engine.JRVisitor;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JRFillElementGroup.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class JRFillElementGroup implements JRElementGroup, JRFillCloneable
{


	/**
	 *
	 */
	protected List<JRChild> children = new ArrayList<JRChild>();
	protected JRElementGroup elementGroup;

	/**
	 *
	 */
	protected JRFillElement[] elements;

	/**
	 *
	 */
	private JRElement topElementInGroup;
	private JRElement bottomElementInGroup;
	private int stretchHeightDiff;


	/**
	 *
	 */
	protected JRFillElementGroup(
		JRElementGroup elementGrp, 
		JRFillObjectFactory factory
		)
	{
		if (elementGrp != null)
		{
			factory.put(elementGrp, this);

			/*   */
			List<JRChild> list = elementGrp.getChildren();
			if (list != null && list.size() > 0)
			{
				for(int i = 0; i < list.size(); i++)
				{
					JRChild child = list.get(i);
					child = (JRChild)factory.getVisitResult(child);
					children.add(child);
				}
			}
	
			/*   */
			getElements();
	
			elementGroup = (JRElementGroup)factory.getVisitResult(elementGrp.getElementGroup());
		}
	}

	
	protected JRFillElementGroup(JRFillElementGroup elementGrp, JRFillCloneFactory factory)
	{
		factory.put(elementGrp, this);

		List<JRChild> list = elementGrp.getChildren();
		if (list != null)
		{
			for (int i = 0; i < list.size(); i++)
			{
				JRFillCloneable child = (JRFillCloneable) list.get(i);
				JRFillCloneable clone = child.createClone(factory);
				children.add((JRChild)clone);
			}
		}

		getElements();

		elementGroup = (JRFillElementGroup) factory.getClone((JRFillElementGroup) elementGrp.getElementGroup());
	}


	/**
	 * 
	 */
	public List<JRChild> getChildren()
	{
		return this.children;
	}


	/**
	 *
	 */
	public JRElementGroup getElementGroup()
	{
		return this.elementGroup;
	}


	/**
	 *
	 */
	public JRElement[] getElements()
	{
		if (this.elements == null)
		{
			if (this.children != null)
			{
				List<JRElement> allElements = new ArrayList<JRElement>();
				Object child = null;
				JRElement[] childElementArray = null;
				for(int i = 0; i < this.children.size(); i++)
				{
					child = this.children.get(i);
					if (child instanceof JRFillElement)
					{
						allElements.add((JRElement)child);
					}
					else if (child instanceof JRFillElementGroup)
					{
						childElementArray = ((JRFillElementGroup)child).getElements();
						if (childElementArray != null)
						{
							allElements.addAll( Arrays.asList(childElementArray) );
						}
					}
				}
				
				this.elements = new JRFillElement[allElements.size()];
				allElements.toArray(this.elements);
			}
		}
		
		return this.elements;
	}


	/**
	 *
	 */
	public JRElement getElementByKey(String key)
	{
		return null;
	}


	/**
	 *
	 */
	protected void reset()
	{
		topElementInGroup = null;
	}


	/**
	 *
	 */
	protected int getStretchHeightDiff()
	{
		if (topElementInGroup == null)
		{
			stretchHeightDiff = 0;
			
			setTopBottomElements();

			JRElement[] allElements = getElements();

			if (allElements != null && allElements.length > 0)
			{
				JRFillElement topElem = null;
				JRFillElement bottomElem = null;

				for(int i = 0; i < allElements.length; i++)
				{
					JRFillElement element = (JRFillElement)allElements[i];
					//if (element != this && element.isToPrint())
					if (element.isToPrint())
					{
						if (
							topElem == null ||
							(
							element.getRelativeY() + element.getStretchHeight() <
							topElem.getRelativeY() + topElem.getStretchHeight())
							)
						{
							topElem = element;
						}

						if (
							bottomElem == null ||
							(
							element.getRelativeY() + element.getStretchHeight() >
							bottomElem.getRelativeY() + bottomElem.getStretchHeight())
							)
						{
							bottomElem = element;
						}
					}
				}

				if (topElem != null)
				{
					stretchHeightDiff = 
						bottomElem.getRelativeY() + bottomElem.getStretchHeight() - topElem.getRelativeY() -
						(bottomElementInGroup.getY() + bottomElementInGroup.getHeight() - topElementInGroup.getY());
				}

				if (stretchHeightDiff < 0)
				{
					stretchHeightDiff = 0;
				}
			}
		}
		
		return stretchHeightDiff;
	}


	/**
	 *
	 */
	private void setTopBottomElements()
	{
		JRElement[] allElements = getElements();
	
		if (allElements != null && allElements.length > 0)
		{
			for(int i = 0; i < allElements.length; i++)
			{
				if (
					topElementInGroup == null ||
					(
					allElements[i].getY() + allElements[i].getHeight() <
					topElementInGroup.getY() + topElementInGroup.getHeight())
					)
				{
					topElementInGroup = allElements[i];
				}

				if (
					bottomElementInGroup == null ||
					(
					allElements[i].getY() + allElements[i].getHeight() >
					bottomElementInGroup.getY() + bottomElementInGroup.getHeight())
					)
				{
					bottomElementInGroup = allElements[i];
				}
			}
		}
	}


	/**
	 *
	 */
	public void visit(JRVisitor visitor)
	{
		visitor.visitElementGroup(this);
	}

	
	public JRFillCloneable createClone(JRFillCloneFactory factory)
	{
		return new JRFillElementGroup(this, factory);
	}

	/**
	 *
	 */
	public Object clone() 
	{
		throw new UnsupportedOperationException();
	}

	/**
	 *
	 */
	public Object clone(JRElementGroup parentGroup) 
	{
		throw new UnsupportedOperationException();
	}
}
