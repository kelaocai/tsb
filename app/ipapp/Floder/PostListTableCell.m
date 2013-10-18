//
//  PostListTableCell.m
//  ipapp
//
//  Created by kelaocai on 13-10-16.
//  Copyright (c) 2013年 tongshibang. All rights reserved.
//

#import "PostListTableCell.h"
#import <QuartzCore/QuartzCore.h>
@implementation PostListTableCell



- (id)initWithStyle:(UITableViewCellStyle)style reuseIdentifier:(NSString *)reuseIdentifier
{
    self = [super initWithStyle:style reuseIdentifier:reuseIdentifier];
    if (self) {
        // Initialization code
        self.messageFontSize=14.0f;
        self.avatar = [[[UIImageView alloc] initWithFrame:CGRectMake(15, 5, 36, 36)] autorelease];
        self.avatar.backgroundColor = [UIColor clearColor];
        //设置圆角
        self.avatar.layer.cornerRadius = 8;
        self.avatar.layer.masksToBounds = YES;
        //自适应图片宽高比例
        self.avatar.contentMode = UIViewContentModeScaleAspectFit;
        [self.contentView addSubview:self.avatar];
        //message
        self.message=[[UILabel alloc] initWithFrame:CGRectMake(85, 5, 200,20)];
        self.message.numberOfLines=0;
        self.message.font=[UIFont systemFontOfSize:_messageFontSize];
        self.message.textAlignment=NSTextAlignmentLeft;
        //文字背景
        UIEdgeInsets insets=UIEdgeInsetsMake(16.0f, 22.0f, 14.0f, 12.0f);
        UIImage *msg_bg_img=[[UIImage imageNamed:@"pop_bg"] resizableImageWithCapInsets:insets resizingMode:UIImageResizingModeStretch];
        self.messageBgView=[[[UIImageView alloc] initWithImage:msg_bg_img] autorelease];
        self.messageBgView.frame=CGRectMake(80, 5, 450, 200);
        self.messageBgView.contentMode = UIViewContentModeScaleToFill;
        //self.messageBgView.alpha=0.8f;
        [self.contentView addSubview:self.messageBgView];
        self.message.backgroundColor=[UIColor clearColor];
        self.message.lineBreakMode=NSLineBreakByWordWrapping;
        [self.contentView addSubview:self.message];
        //作者+时间戳
        self.author=[[UILabel alloc] initWithFrame:CGRectMake(85, 25, 200,20)];
        self.author.font=[UIFont systemFontOfSize:12.0f];
        self.author.textColor=[UIColor grayColor];
        [self.contentView addSubview:self.author];
        
        
        
    }
    return self;
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated
{
    [super setSelected:selected animated:animated];

    // Configure the view for the selected state
}

@end
